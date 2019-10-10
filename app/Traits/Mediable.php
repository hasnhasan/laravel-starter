<?php

namespace App\Traits;

trait Mediable
{
    use \Plank\Mediable\Mediable;

    /**
     * Kaydetme veya silme işlemi olduğunda medya ekle veya sil
     */
    protected static function bootMediable()
    {
        static::saved(function(self $model) {
            $media = request()->get('_media', []);
            if ($media) {
                foreach ($media as $mediaTag => $media) {
                    if (!isset($media['id']) || !$media['id']) {
                        continue;
                    }
                    $model->syncMedia($media['id'], $mediaTag, $media['alt']);
                }
            }
        });

        static::deleted(function(self $model) {
            $model->handleMediableDeletion();
        });
    }

    /**
     * Alt tagı eklemek için Plank Mediable kütüphanesini genişlet
     *
     * @return mixed
     */
    public function media()
    {
        return $this
            ->morphToMany(
                config('mediable.model'),
                'mediable',
                config('mediable.mediables_table', 'mediables')
            )
            ->withPivot('tag', 'order', 'alt')
            ->orderBy('order');
    }

    /**
     * * Alt tagı eklemek için Plank Mediable kütüphanesini genişlet
     *
     * @param $media
     * @param $tags
     * @param null $alt
     */
    public function attachMedia($media, $tags, $alt = NULL)
    :void {
        $tags       = (array)$tags;
        $increments = $this->getOrderValueForTags($tags);

        $ids = $this->extractPrimaryIds($media);

        foreach ($tags as $tag) {
            $attach = [];
            foreach ($ids as $id) {
                $attach[$id] = [
                    'tag'   => $tag,
                    'alt'   => $alt,
                    'order' => ++$increments[$tag],
                ];
            }
            $this->media()->attach($attach);
        }

        $this->markMediaDirty($tags);
    }

    /**
     * @param $media
     * @param $tags
     * @param null $alt
     */
    public function syncMedia($media, $tags, $alt = NULL)
    :void {
        $this->detachMediaTags($tags);
        $this->attachMedia($media, $tags, $alt);
    }
}
