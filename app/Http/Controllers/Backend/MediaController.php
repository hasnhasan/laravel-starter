<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\BackendController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Plank\Mediable\Media;
use Plank\Mediable\MediaUploaderFacade;
use Plank\Mediable\SourceAdapters\SourceAdapterInterface;

class MediaController extends BackendController
{
    /**
     * Dosya ve Klasör Yönetimi
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Request $request)
    {
        return view('backend.media-manager.list');
    }

    /**
     * Editörlerde kullanmak üzere popup halinde dosya yönetimi
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function popup(Request $request)
    {
        $includeFile = 'backend.media-manager.browser';

        return view('backend.partials.popup', compact('includeFile'));
    }

    /**
     * Dosya veya klasör için yükleme/oluşturma,düzenleme,silme ve isim değiştirme işlemleri
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function mediaActions(Request $request)
    {
        $dirPath  = config('filesystems.disks.public.root').'/';
        $argument = json_decode($request->get('arguments', '[]'), 1);

        // Dosya Yükle
        if ($request->get('command') == 'UploadChunk') {
            return $this->uploadFiles($request);
        }

        // Dosya veya Klasör ismi değiştir
        if ($request->get('command') == 'Rename') {
            $file     = $dirPath.$argument['id'];
            $name     = $argument['name'];
            $realPath = str_replace(basename($file), '', $file);
            $newFile  = $realPath.$name;
            $status   = false;

            if ($file != $newFile) {
                $status = File::move($file, $newFile);
                $media  = $this->getMedia($dirPath, basename($file), $realPath);
                if ($media) {
                    $newFileInfo      = pathinfo($newFile);
                    $media->filename  = $newFileInfo['filename'];
                    $media->extension = $newFileInfo['extension'];
                    $media->save();
                }
            }

            return response()->json(['success' => $status, 'errorId' => NULL]);
        }

        // Dosya veye Klasör taşı
        if ($request->get('command') == 'Move') {
            $source = $dirPath.$argument['sourceId'];
            $target = $dirPath.$argument['destinationId'];
            $status = File::move($source, $target);
            if ($status) {
                $baseName = basename($source);
                $media    = $this->getMedia($dirPath, $baseName, str_replace($baseName, '', $source));
                if ($media) {
                    $directory        = str_replace([$dirPath, $baseName], '', $target);
                    $media->directory = trim($directory, '/');
                    $media->save();
                }
            }

            return response()->json(['success' => $status, 'errorId' => NULL]);
        }

        // Dosya veye Klasör Sil
        if ($request->get('command') == 'Remove') {
            $source = $dirPath.$argument['id'];

            if (File::isFile($source)) {
                $status   = File::delete($source);
                $baseName = basename($source);
                $media    = $this->getMedia($dirPath, $baseName, str_replace($baseName, '', $source));
                if ($media) {
                    $media->delete();
                }
            } else {
                $status = File::deleteDirectory($source);
            }

            return response()->json(['success' => $status, 'errorId' => NULL]);
        }

        // Dosya veye Klasör kopyala
        if ($request->get('command') == 'Copy') {
            $source = $dirPath.$argument['sourceId'];
            $target = $dirPath.$argument['destinationId'];
            if (File::isFile($source)) {
                $status = File::copy($source, $target);
            } else {
                $status = File::copyDirectory($source, $target);
            }

            return response()->json(['success' => $status, 'errorId' => NULL]);
        }

        // Klasör oluştur
        if ($request->get('command') == 'CreateDir') {
            $folder = $dirPath.$argument['parentId'].'/'.$argument['name'];
            $status = File::makeDirectory($folder);

            return response()->json(['success' => $status, 'errorId' => NULL]);
        }

        // Dosya ve klasörleri listele
        if ($request->get('command') == 'GetDirContents') {

            $fileAndFolder = [
                'success' => true,
                'errorId' => NULL,
                'result'  => [],
            ];

            $dirPathO    = $dirPath.$argument['parentId'];
            $directories = new \DirectoryIterator($dirPathO);
            foreach ($directories as $file) {
                if ($file->isDot()) {
                    continue;
                }
                $hasSubDirectories = false;
                $mediaId           = NULL;
                $mediaUrl          = NULL;
                if ($file->isDir()) {
                    $checkSubDir       = File::directories($file->getRealPath());
                    $hasSubDirectories = (bool)count($checkSubDir);

                } else {
                    $media = $this->getMedia($dirPath, $file->getBasename(), $file->getRealPath());
                    if ($media) {
                        $mediaId  = $media->id;
                        $mediaUrl = $media->getUrl();
                    }
                }

                $fileAndFolder['result'][] = [
                    'name'              => $file->getBasename(),
                    'dateModified'      => \Carbon\Carbon::parse($file->getMTime())->format('d/m/Y H:i:s'),
                    'isDirectory'       => $file->isDir(),
                    'size'              => $file->getSize(),
                    'hasSubDirectories' => $hasSubDirectories,
                    'mediaId'           => $mediaId,
                    'url'               => $mediaUrl,
                ];
            }

            return response()->json($fileAndFolder);
        }
    }

    /**
     * Dosya Yükleme işlemleri
     * TODO:: yüklenebilecek uzantıları ayarlamak gerekiyor.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    private function uploadFiles(Request $request)
    {
        $argument          = json_decode($request->get('arguments', '[]'), 1);
        $tempFilesLocation = sys_get_temp_dir();
        $targetLocation    = $argument['destinationId'];
        try {
            // Checks whether the array of uploaded files exists
            // Here, "file" is a string specified in the FileUploader's "name" option
            if (!$request->file('chunk')) {
                throw new \Exception('File is not specified');
            }

            if (isset($argument['chunkMetadata'])) {
                // Gets chunk details
                $metaDataObject = json_decode($argument['chunkMetadata']);

                // ...
                // Perform security checks here
                // ...

                // Creates a directory for temporary files if it does not exist
                if (!file_exists($tempFilesLocation)) {
                    mkdir($tempFilesLocation);
                }

                $tempFilePath = $tempFilesLocation."/".$metaDataObject->UploadId.".temp";

                // Appends the chunk to the file
                $content = file_get_contents($request->file('chunk')->getPathName());

                file_put_contents($tempFilePath, $content, FILE_APPEND);

                // Checks that the file size does not exceed the allowed size
                if (filesize($tempFilePath) > 1024 * 400000) {
                    throw new \Exception('File is too large');
                }

                // Saves the file if all chunks are received
                if ($metaDataObject->Index == ($metaDataObject->TotalCount - 1)) {
                    $fileDetail = pathinfo($metaDataObject->FileName);
                    try {
                        $media = MediaUploaderFacade::fromSource($tempFilePath)
                            ->toDirectory($targetLocation)
                            ->useFilename(str_slug($fileDetail['filename']))
                            ->beforeSave(function(Media $model, SourceAdapterInterface $source) use ($fileDetail) {
                                $model->extension = $fileDetail['extension'];
                            })
                            ->upload();
                    } catch (\Exception $e) {
                        throw new \Exception($e->getMessage());
                    }
                }
            }
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'errorId' => $e->getMessage()]);
        }

        return response()->json(['success' => true, 'errorId' => NULL]);
    }

    /**
     * Dosyaların DB deki karşılıklarını getirmeye yarar.
     *
     * @param $dirPath
     * @param $baseName
     * @param $realPath
     * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model|object|null
     */
    private function getMedia($dirPath, $baseName, $realPath)
    {
        $directory = str_replace([$dirPath, $baseName], '', $realPath);

        return Media::whereBasename($baseName)->where('directory', $directory)->first();
    }
}
