<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

trait Filterable
{
    /**
     * Filtrelemede gelen operator için özel fonksiyon tanımlamaları
     *
     * @var array
     */
    protected $operatorScope = [
        'contains'    => 'contains',
        'notcontains' => 'notContains',
        'startswith'  => 'startsWith',
        'endswith'    => 'endsWith',
        'isblank'     => 'isBlank',
        'isnotblank'  => 'isNotBlank',
    ];
    /**
     * Veritabanındaki kolon adları ismiyle gelen isimleri eşleştirir
     *
     * @var array
     */
    protected $columnsMerger = [];
    /**
     * Sorgu da And mi Or mu kullanacağını belirler
     *
     * @var string
     */
    protected $operatorType = 'and';
    /**
     * Elequent için $this->operatorType e göre "where" fonksiyonunu ayarlar
     *
     * @var array
     */
    protected $operatorFunc = ['and' => 'where', 'or' => 'orWhere'];

    /**
     * Filtreleme ve sıralama yapmak için kullanılır.
     * Datagrid kullanılmasa da parametrelerle beraber ayrı olarak ta kullanılabilir.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeFilterAndSort(Builder $query)
    {
        // Sorguda Select bölümünde isimler değiştirilmişse orjinal isimlerini kullanmaya yarar

        collect($query->getQuery()->columns)->map(function($field) {
            $fieldParse                      = preg_split("/ as /i", $field); #explode(' as ', $field);
            $fieldName                       = end($fieldParse);
            $this->columnsMerger[$fieldName] = trim($fieldParse[0]);
        });

        // Sıralama
        $sorts = json_decode(request()->get('sort', '[]'));
        $query = $this->sortWithQuery($query, $sorts);

        // Filtreleme
        $filters = json_decode(request()->get('filter', '[]'), 1);
        $query   = $this->filterWithQuery($query, $filters);

        return $query;
    }

    /**
     * Elequent'i işleyerek Datagrid çıktısı üretir.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Support\Collection|\Tightenco\Collect\Support\Collection
     */
    public function scopeRender(Builder $query)
    {
        // Sonuç olarak dönecek veri
        $data = ['data' => []];

        // Filtrelemeyi dahil et
        $query = $query->filterAndSort();

        // Özet çıkartmak için sorguyu klonla
        $summaryQuery = clone $query;

        // Gruplama varsa
        $groups = json_decode(request()->get('group', '[]'), 1);
        if ($groups) {
            $data = $this->groupWithQuery($query, $groups);
        } else {
            $totalCount         = $query->count();
            $data['data']       = $query->skip(request()->skip ?: 0)->take(request()->take ?: 1000)->get()->toArray();
            $data['totalCount'] = $totalCount;
            $data['total']      = $totalCount;
        }

        // Özet varsa
        $totalSummary = json_decode(request()->get('totalSummary', '[]'), 1);
        $summaries    = $this->getSummary($summaryQuery, $totalSummary);
        if ($summaries) {
            $data['summary'] = $summaries;
        }

        return collect($data);
    }

    /**
     * Array olarak gelen sıralama verisini Elequent sorgusuna dönüştürür.
     *
     * @param $query
     * @param array $sorts
     * @return mixed
     */
    private function sortWithQuery($query, $sorts = [])
    {
        foreach ($sorts as $sort) {
            // Sıralama Yapılacak field ismini alır
            $fieldName = $this->findFieldName($sort->selector);

            // Model içerisinde belirtilen bir sayı alanı varsa 1,10,11,2 gibi gitmemesi için fix | public $numericOrder = [];
            if (isset($this->numericOrder[$fieldName])) {

                if ($sort->desc) {
                    $query->orderByRaw('LENGTH('.$fieldName.') DESC');
                } else {
                    $query->orderByRaw('LENGTH('.$fieldName.') ASC');
                }
                continue;
            }

            // Sıralama Türüne göre Asc veya Desc
            if ($sort->desc) {
                $query->orderByDesc($fieldName);
            } else {
                $query->orderBy($fieldName);
            }

        }

        return $query;
    }

    /**
     * Array olarak gelen Filtreleme verisini Elequent sorgusuna dönüştürür.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param $filters
     * @return mixed
     */
    #TODO:: with ile gelen sorgularda filtreleme yapamıyor.
    private function filterWithQuery(Builder $query, $filters)
    {

        // where veya orWhere mi olacağını seçer
        $whereType = $this->operatorFunc[$this->operatorType];

        $query = $query->$whereType(function($query) use ($filters) {
            return $this->filterProcess($query,$filters);
        });

        return $query;
    }

    private function filterProcess($query, $filters)
    {
        $prevItemWasArray = false; #Bir önceki $filters değişkeni array mi?
        foreach ($filters as $index => $item) {

            // Filtreleme Grup içinde değilse
            if (is_string($item)) {
                $prevItemWasArray = false;

                if ($index == 0) {
                    if ($item == "!") {
                        continue;
                    }

                    // Sorguları işle
                    if (isset($filters) && is_array($filters)) {

                        list($field, $operator, $value) = $filters;

                        // null Value fix
                        if ($value == '(Null)') {
                            $value = NULL;
                        }
                        // Sıralama Yapılacak field ismini alır
                        $field = $this->findFieldName($field);

                        // Özel fonksiyonu varmı ?
                        if (isset($this->operatorScope[$operator])) {
                            $operator = $this->operatorScope[$operator];
                            $query    = $query->$operator($field, $value);

                            return $query;
                        }

                        // Düz sorgu
                        return $query->where($field, $operator, $value);

                    }
                    break;
                }

                // Operatörü Ekle
                $this->operatorType = trim($item);
                continue;
            }

            // Filtreleme Grup içerisindeyse
            if (is_array($item)) {
                if ($prevItemWasArray) {
                    $this->operatorType = 'and';
                }
                $this->filterWithQuery($query, $item);
                $prevItemWasArray = true;
            }
        }

        return $query;
    }

    /**
     * Array olarak gelen grup verisini Elequent sorgusuna dönüştürür.
     *
     * @param $query
     * @param array $groups
     * @return array
     */
    #TODO:: with ile gelen sorgularda grup yapamıyor.
    private function groupWithQuery(Builder $query, $groups = [])
    {

        $data = ['data' => []];

        foreach ($groups as $group) {
            // Field
            $selector = $group['selector'];
            // Sıralama Durumu
            $orderByDesc = $group['desc'];
            // orjinal field adını getir
            $fieldName = $this->findFieldName($selector);

            // Toplam sayıyı al
            $totalCount = $query->count();
            // Grupla
            $gQuery = $query->groupBy($fieldName);

            // Sıralama
            $gQuery->getQuery()->orders = NULL;
            if ($orderByDesc) {
                $gQuery = $gQuery->orderByDesc($fieldName);
            } else {
                $gQuery = $gQuery->orderBy($fieldName);
            }

            // Grup Özetleri
            $groupSummary = json_decode(request()->get('groupSummary', '[]'), 1);
            $summaries    = $this->getSummary($gQuery, $groupSummary, $fieldName);

            // Gruplar
            $groupDatas = $gQuery->selectRaw($fieldName.' as g__filed,count(*) as g__total')->get();

            // Gruplama içerisinde kaç grup var?
            $data['groupCount'] = $groupDatas->count();

            foreach ($groupDatas->toArray() as $item) {

                $gDataCount = $item['g__total'];
                $gField     = $item['g__filed'];
                $sField     = $gField;
                if (!$gField) {
                    $gField = NULL;
                }
                if (!$sField) {
                    $sField = 'S_NULL';
                }

                $summary = [];
                // Grup için bir özet varsa getir
                if (isset($summaries[$sField])) {
                    $summary = $summaries[$sField];
                }

                // Datagrid için colum oluştur
                array_push($data['data'], [
                    'key'     => $gField,
                    'summary' => $summary,
                    'items'   => NULL,
                    'count'   => $gDataCount,
                ]);
            }

        }
        $data['totalCount'] = $totalCount;
        $data['total']      = $totalCount;

        return $data;
    }

    /**
     * Özet bilgisini çıkartır.
     * @link https://js.devexpress.com/Documentation/Guide/Widgets/DataGrid/Summaries/
     *
     * @param $query
     * @param array $summaryData
     * @param null $groupField
     * @return array
     */
    private function getSummary($query, $summaryData = [], $groupField = NULL)
    {
        $summaries = [];
        foreach ($summaryData as $summary) {
            // Sorguların karışmaması için orjinal sorguyu klonla
            $groupSummaryQuery = clone $query;
            // özet için kullanılacak fieldın orjinal adını bul
            $summaryField = $this->findFieldName($summary['selector']);

            // Özet sorgusu için select oluştur
            $selectField = $groupField;
            if (!$selectField) {
                $selectField = $summaryField;
            }
            $summarySelect = "$selectField AS s__field ,";

            // Özet tiplerine göre sql sorgusu hazırla
            switch ($summary['summaryType']) {
                case 'count':
                    $summarySelect .= "COUNT(*) AS s__data";
                    break;

                case 'sum':
                    $summarySelect .= "SUM($summaryField) AS s__data";
                    break;

                case 'min':
                    $summarySelect .= "MIN($summaryField) AS s__data";
                    break;

                case 'max':
                    $summarySelect .= "MAX($summaryField) AS s__data";
                    break;

                case 'avg':
                    $summarySelect .= "AVG($summaryField) AS s__data";
                    break;
                case 'custom':#TODO::Custom summary type
                    break;

            }

            // Özet sorgunusu çalıştır
            $summaryData = $groupSummaryQuery->selectRaw($summarySelect)->pluck('s__data', 's__field')->toArray();

            // Özet verilerini gruplara göre işle
            foreach ($summaryData as $sField => $sValue) {
                if (!$sField) {
                    $sField = 'S_NULL';
                }
                // Gruplama için kullanılıyorsa
                if ($groupField) {
                    $summaries[$sField][] = $sValue;
                    continue;
                }
                // TotalSummary için kullanılıyorsa
                $summaries[] = $sValue;
            }

        }

        return $summaries;
    }

    /**
     * Field adının Veritabanındaki orjinal haline elequent de kullanılacak halde döndürür.
     *
     * @param $fieldName
     * @return \Illuminate\Database\Query\Expression
     */
    private function findFieldName($fieldName)
    {
        // Field adının dbdeki adını getirir
        if (isset($this->columnsMerger[$fieldName])) {
            $fieldName = $this->columnsMerger[$fieldName];
        }

        // Db adı dışında fonksiyon olarak oluşturulmuş bir fieldsa raw olarak field adını güncelle
        if (strstr($fieldName, '(') && strstr($fieldName, ')') || strstr($fieldName, '/')) {
            $fieldName = DB::raw($fieldName);
        }

        return $fieldName;
    }
    ###################--Filtreleme için özel fonksiyonlar--#########################

    /**
     * @param $query
     * @param $field
     * @param string $value
     * @return mixed
     */
    public function scopeIsBlank($query, $field, $value = '')
    {
        $whereType = $this->operatorFunc[$this->operatorType].'Null';

        return $query->$whereType($field, $value);
    }

    /**
     * @param $query
     * @param $field
     * @param string $value
     * @return mixed
     */
    public function scopeIsNotBlank($query, $field, $value = '')
    {
        $whereType = $this->operatorFunc[$this->operatorType].'NotNull';

        return $query->$whereType($field, $value);
    }

    /**
     * @param $query
     * @param $field
     * @param string $value
     * @return mixed
     */
    public function scopeContains($query, $field, $value = '')
    {
        $whereType = $this->operatorFunc[$this->operatorType];

        return $query->$whereType($field, 'LIKE', "%$value%");
    }

    /**
     * @param $query
     * @param $field
     * @param string $value
     * @return mixed
     */
    public function scopeNotContains($query, $field, $value = '')
    {
        $whereType = $this->operatorFunc[$this->operatorType];

        return $query->$whereType($field, 'NOT LIKE', "%$value%");
    }

    /**
     * @param $query
     * @param $field
     * @param string $value
     * @return mixed
     */
    public function scopeStartsWith($query, $field, $value = '')
    {
        $whereType = $this->operatorFunc[$this->operatorType];

        return $query->$whereType($field, 'LIKE', "$value.%");
    }

    /**
     * @param $query
     * @param $field
     * @param string $value
     * @return mixed
     */
    public function scopeEndsWith($query, $field, $value = '')
    {
        $whereType = $this->operatorFunc[$this->operatorType];

        return $query->$whereType($field, 'LIKE', "%$value");
    }
}
