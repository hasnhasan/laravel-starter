<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\BackendController;
use App\Models\Article;
use App\Models\DynamicRoute;
use App\Utils\DataGrid;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ArticleController extends BackendController
{
    /**
     * Article tablosundaki içeriklerin listelenmesi ve filtrelenmesi işlemini sağlar
     *
     * @param string $module
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\JsonResponse|\Illuminate\View\View
     */
    public function index($module = NULL, Request $request)
    {
        // Ajax istedi geldiğinde datagrid için sonuç döndür
        if ($request->ajax()) {
            $datas = Article::when($module, function($q, $module) {
                return $q->where('module', '=', $module);
            })->render();

            return response()->json($datas);
        }

        $modules = [
            'default'   => __('İçerikler'),
            'servisler' => __('Servisler'),
        ];
        if ($module === NULL) {
            $module = 'default';
        }

        $dataGrid = (new DataGrid())
            ->setAjaxUrl(route('article.list', $module))
            ->setRemoveUrl(route('article.delete'))
            ->addColumn('title', __('Başlık'))
            ->addColumn('route.slug', __('Slug'))
            ->addColumn('published_date', __('Yayınlanma Tarihi'))
            ->addColumnDropdown('status', __('Durum'), [
                'Active'  => __('Aktif'),
                'Passive' => __('Pasif'),
                'Draft'   => __('Taslak'),
            ])
            ->addButton('Düzenle', '', route('article.update'), 'id')
            ->render();

        return view('backend::article.list', compact('dataGrid', 'modules', 'module'));
    }

    /**
     * @param null $articleId
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function createOrUpdateForm($articleId = NULL)
    {
        if ($articleId && is_numeric($articleId)) {
            $article = Article::findOrFail($articleId);
        } else {
            $article = Article::newModelInstance();
        }

        $useImage = [
            'feature' => 'Öne Çıkan Görsel',
            'detail'  => 'Görseller',
        ];

        return view('backend::article.form', compact('article', 'articleId', 'useImage'));
    }

    /**
     * @param $id
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function createOrUpdate($id, Request $request)
    {
        $request->validate([
            'title'          => 'required',
            'status'         => 'required',
            'published_date' => 'required|date',
            'expiry_date'    => 'date',
        ]);
        $formData = $request->all();

        $formData['site_id'] = 1;
        $formData['user_id'] = Auth::id();
        $article             = Article::updateOrCreate(['id' => $id], $formData);

        return redirect()->back();
    }

    /**
     * İçerik Silme işlemi
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function delete(Request $request)
    {
        $status = Article::find($request->get('key'))->delete();

        return response()->json(['status' => $status]);
    }
}
