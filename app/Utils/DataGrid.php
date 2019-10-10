<?php

namespace App\Utils;

class DataGrid
{
    public $dxDatagridVersion = '19.1.6';
    /**
     * Js tarafında istek yapacak değişkenin adı. Bu değilen aynı sayfada birden fazla datagrid kullanılacak ise çakışmaması için değiştirilmelidir.
     *
     * @var string
     */
    protected $dataSourceName;
    /**
     * Datagrid hangi element'e oluşturulması gerektiğini belirtir.
     *
     * @var string
     */
    protected $selectorElement = '';
    /**
     * Datagrid Temasını belirler.
     *
     * @var string
     */
    protected $themeCss = 'dx.light.css';
    /**
     * Her sayfada kaç adet içerik olacağını belirler
     *
     * @var int
     */
    protected $pageSize = 50;
    /**
     * Js tarafında yapılacak işlemleri belirler.
     *
     * @link https://js.devexpress.com/Documentation/Guide/Widgets/DataGrid/Data_Binding/Custom_Sources/
     *
     * @var array
     */
    protected $dataSourceOptions = [
        'key'             => 'id', #id olarak kullanılacak field ı belirler
        'url'             => '', #Verileri çekmek için ajax isteği yapacağı link
        'insertUrl'       => '', #Kaydetmek için ajax isteği yapacağı link
        'updateUrl'       => '', #Düzenlemek için ajax isteği yapacağı link
        'removeUrl'       => '', #Silmek için ajax isteği yapacağı link
        'successCallback' => '', #Başarılı olduğunda hook atmak istersek kullanabiliriz.(Js fonksiyonu)
        'errorCallback'   => '', #Hatalı olduğunda hook atmak istersek kullanabiliriz.(Js fonksiyonu)
        'insertCallback'  => '', #CRUD Ekleme işlemi için hook atmak istersek kullanabiliriz.(Js fonksiyonu)
        'updateCallback'  => '', #CRUD Güncelleme işlemi için hook atmak istersek kullanabiliriz.(Js fonksiyonu)
        'deleteCallback'  => '', #CRUD Silme işlemi için hook atmak istersek kullanabiliriz.(Js fonksiyonu)
        'timeout'         => 99000, #Ajax isteklerinin zaman aşımı süresi
    ];
    /**
     * Varsayılan dxDataGrid ayarları
     *
     * @link https://js.devexpress.com/Documentation/ApiReference/UI_Widgets/dxDataGrid/Configuration/
     * @link https://js.devexpress.com/Documentation/Guide/Widgets/DataGrid/Paging/
     *
     * @var array
     */
    protected $options = [
        'paging'                => ['pageSize' => 50],
        'cacheEnabled'          => false,
        'headerFilter'          => false,
        'wordWrapEnabled'       => true,
        'showBorders'           => true,
        'rowAlternationEnabled' => true,
        'filterSyncEnabled'     => true,
        'filterPanel'           => ['visible' => true],
        'remoteOperations'      => ['groupPaging' => true],
        'filterBuilderPopup'    => [
            'position'    => ['at' => 'top', 'my' => 'top', 'offset' => ['y' => 10]],
            'applyFilter' => 'auto',
        ],
        'grouping'              => ['expandMode' => 'rowClick', 'autoExpandAll' => false],
        'groupPanel'            => ['visible' => true],
        'searchPanel'           => ['visible' => false, 'width' => 240, 'placeholder' => 'Ara...'],
        'filterRow'             => ['visible' => true, 'applyFilter' => 'auto'],
        'focusedRowEnabled'     => true,
        'export'                => ['enabled' => true],
        'columnResizingMode'    => "widget",
        'allowColumnResizing'   => true,
        'columnMinWidth'        => 100,
        'columnAutoWidth'       => true,
        'columnChooser'         => ['enabled' => true, 'mode' => 'select',],
        'stateStoring'          => ['enabled' => false,],#Kullanıcının Son kaldığı yeri hatırlamasını sağlar.
        'editing'               => [
            'refreshMode' => 'full',
            'mode'        => 'popup',
            'popup'       => [
                'title'     => '',
                'showTitle' => false,
            ],
        ],
        'masterDetail'          => [
            'enabled' => false,
        ],
    ];
    /**
     * varsayılan dxDataGrid Kolon Ayarları
     * addColumn() fonksiyonu kullanıldığında aşağıdaki tanımlamaları ekler.
     *
     * https://js.devexpress.com/Documentation/Guide/Widgets/DataGrid/Columns/Overview/
     * @var array
     */
    protected $defaultColumns = [
        'caption'              => '',
        'dataField'            => '',
        'dataType'             => 'string',#['number', 'boolean', 'string', 'date', 'datetime'],
        'alignment'            => '',#['right', 'center', 'left'],
        'buttons'              => '',#['cancel', 'delete', 'edit', 'save', 'undelete'],
        'lookup'               => [
            'dataSource' => [],
        ],
        'format'               => [
            'type'     => '',
            'currency' => '',
        ],
        'allowExporting'       => true,
        'allowFiltering'       => true,
        'allowEditing'         => false,
        'allowFixing'          => false,
        'allowGrouping'        => true,
        'allowHeaderFiltering' => false,
        //'allowHiding'          => false,
        'allowReordering'      => false,
        'allowResizing'        => true,
        'allowSearch'          => false,
        'allowSorting'         => true,
        'autoExpandGroup'      => false,
    ];
    /**
     * Oluşturulacak Kolonları tutan değişken
     *
     * @var array
     */
    protected $columns = [];
    /**
     * Crud için form detaylarını tutan değişken
     *
     * @var array
     */
    protected $forms = [];

    /**
     * DataGrid constructor.
     *
     * @param string $sourceName Js tarafında istek yapacak değişkenin adı. Bu değilen aynı sayfada birden fazla datagrid kullanılacak ise çakışmaması için değiştirilmelidir.
     * @param string $selectorElement Datagrid hangi element'e oluşturulması gerektiğini belirtir.
     */
    public function __construct($sourceName = 'dataGrid_table', $selectorElement = '#gridContainer')
    {
        $this->dataSourceName  = $sourceName;
        $this->selectorElement = $selectorElement;

        // Özel filtreleri ekle
        $this->addCustomFiter();
    }

    /**
     * Tarih filtresini genişletmek ve dinamik olarak kullanmak için tanımlamalar
     *
     * @link https://js.devexpress.com/Documentation/ApiReference/UI_Widgets/dxFilterBuilder/Configuration/customOperations/
     */
    private function addCustomFiter()
    {
        // Bugün
        $today = now()->format('Y-m-d');
        // Dün
        $yesterDay = now()->subDay()->format('Y-m-d');
        // Bu Hafta
        $weekStart = now()->startOfWeek()->format('Y-m-d');
        $weekEnd   = now()->endOfWeek()->format('Y-m-d');
        // Geçen Hafta
        $lastWeekStart = now()->subWeek()->startOfWeek()->format('Y-m-d');
        $lastWeekhEnd  = now()->subWeek()->endOfWeek()->format('Y-m-d');
        // Bu Ay
        $monthStart = now()->startOfMonth()->format('Y-m-d');
        $monthEnd   = now()->endOfMonth()->format('Y-m-d');
        // Geçen Ay
        $lastMonthStart = now()->subMonth()->startOfMonth()->format('Y-m-d');
        $lastMonthEnd   = now()->subMonth()->endOfMonth()->format('Y-m-d');
        // Bu Yıl
        $yearStart = now()->startOfYear()->format('Y-m-d');
        $yearEnd   = now()->endOfYear()->format('Y-m-d');
        // Geçen Yıl
        $lastYearStart = now()->subYear()->startOfYear()->format('Y-m-d');
        $lastYearEnd   = now()->subYear()->endOfYear()->format('Y-m-d');

        // customOperations alanının alabileceği değerler
        $this->options['filterBuilder'] = [
            'customOperations' => [
                [
                    'name'                      => 'thisDay',
                    'caption'                   => 'Bugün',
                    'dataTypes'                 => 'date',
                    'icon'                      => 'clock',
                    'hasValue'                  => false,
                    'calculateFilterExpression' => 'function(filterValue, field) { return [field.dataField,"=","'.$today.'"]; }',
                ],
                [
                    'name'                      => 'lastDay',
                    'caption'                   => 'Dün',
                    'dataTypes'                 => 'date',
                    'icon'                      => 'clock',
                    'hasValue'                  => false,
                    'calculateFilterExpression' => 'function(filterValue, field) { return [field.dataField,"=","'.$yesterDay.'"]; }',
                ],
                [
                    'name'                      => 'thisWeek',
                    'caption'                   => 'Bu Hafta',
                    'dataTypes'                 => 'date',
                    'icon'                      => 'clock',
                    'hasValue'                  => false,
                    'calculateFilterExpression' => 'function(filterValue, field) { return [[field.dataField,">=","'.$weekStart.'"],"and",[field.dataField,"<=","'.$weekEnd.'"]]; }',
                ],
                [
                    'name'                      => 'lastWeek',
                    'caption'                   => 'Geçen Hafta',
                    'dataTypes'                 => 'date',
                    'icon'                      => 'clock',
                    'hasValue'                  => false,
                    'calculateFilterExpression' => 'function(filterValue, field) { return [[field.dataField,">=","'.$lastWeekStart.'"],"and",[field.dataField,"<=","'.$lastWeekhEnd.'"]]; }',
                ],
                [
                    'name'                      => 'thisMonth',
                    'caption'                   => 'Bu Ay',
                    'dataTypes'                 => 'date',
                    'icon'                      => 'clock',
                    'hasValue'                  => false,
                    'calculateFilterExpression' => 'function(filterValue, field) { return [[field.dataField,">=","'.$monthStart.'"],"and",[field.dataField,"<=","'.$monthEnd.'"]]; }',
                ],
                [
                    'name'                      => 'lastMonth',
                    'caption'                   => 'Geçen Ay',
                    'dataTypes'                 => 'date',
                    'icon'                      => 'clock',
                    'hasValue'                  => false,
                    'calculateFilterExpression' => 'function(filterValue, field) { return [[field.dataField,">=","'.$lastMonthStart.'"],"and",[field.dataField,"<=","'.$lastMonthEnd.'"]]; }',
                ],
                [
                    'name'                      => 'thisYear',
                    'caption'                   => 'Bu Yıl',
                    'dataTypes'                 => 'date',
                    'icon'                      => 'clock',
                    'hasValue'                  => false,
                    'calculateFilterExpression' => 'function(filterValue, field) { return [[field.dataField,">=","'.$yearStart.'"],"and",[field.dataField,"<=","'.$yearEnd.'"]]; }',
                ],
                [
                    'name'                      => 'lastYear',
                    'caption'                   => 'Geçen Yıl',
                    'dataTypes'                 => 'date',
                    'icon'                      => 'clock',
                    'hasValue'                  => false,
                    'calculateFilterExpression' => 'function(filterValue, field) { return [[field.dataField,">=","'.$lastYearStart.'"],"and",[field.dataField,"<=","'.$lastYearEnd.'"]]; }',
                ],
            ],
        ];
    }

    /**
     * DataGrid Çalışması için çıktı üretir.
     *
     * @return string
     */
    public function render()
    {
        // Crud için link veya hook verilmişse dxDataGrid ayarlarından da aktif et

        $checkCrudActive = [
            'insertUrl'      => 'allowAdding',
            'insertCallback' => 'allowAdding',

            'updateUrl'      => 'allowUpdating',
            'updateCallback' => 'allowUpdating',

            'removeUrl'      => 'allowDeleting',
            'deleteCallback' => 'allowDeleting',
        ];
        foreach ($checkCrudActive as $source => $crudOption) {
            if (isset($this->dataSourceOptions[$source]) && $this->dataSourceOptions[$source]) {
                $this->options['editing'][$crudOption] = true;
            }
        }

        // Düzenleme için bir işlem yapılmışsa Düzenleme butonu ekle
        if ((isset($this->dataSourceOptions['updateUrl']) && $this->dataSourceOptions['updateUrl']) ||
            isset($this->dataSourceOptions['updateCallback']) && $this->dataSourceOptions['updateCallback']) {
            $this->addButton('edit');
        }
        // Silme için bir işlem yapılmışsa Düzenleme butonu ekle
        if ((isset($this->dataSourceOptions['removeUrl']) && $this->dataSourceOptions['removeUrl']) ||
            isset($this->dataSourceOptions['deleteCallback']) && $this->dataSourceOptions['deleteCallback']) {
            $this->addButton('delete');
        }

        // Form Varsa birleştirir
        $this->formRender();

        // Ayarları ve Kolonları birleştirir
        $datas = $this->options + ['columns' => $this->columns];

        // Boş arrayleri temizler.
        $dataMerge = array_remove_empty($datas);

        // Datagrid oluşturmak için gerekli js vs css dosyalarını html çıktısı şeklinde ayarlar.
        $jsConvert = '<link rel="stylesheet" type="text/css" href="https://cdn3.devexpress.com/jslib/'.$this->dxDatagridVersion.'/css/dx.common.css"/><link rel="stylesheet" type="text/css" href="https://cdn3.devexpress.com/jslib/'.$this->dxDatagridVersion.'/css/'.$this->themeCss.'"/><style>'.$this->selectorElement.' {max-height: 100vh !important;}</style><script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.2.0/jszip.min.js"></script><script type="text/javascript" src="https://cdn3.devexpress.com/jslib/'.$this->dxDatagridVersion.'/js/dx.all.js"></script><script type="text/javascript" src="https://cdn3.devexpress.com/jslib/19.1.6/js/localization/dx.messages.tr.js"></script><script>DevExpress.localization.locale(navigator.language || navigator.browserLanguage);$.ajaxSetup({headers: {\'X-CSRF-TOKEN\': $(\'meta[name="csrf-token"]\').attr(\'content\')}});</script>';

        // DataGrid ayarlarına Veri çekmek için istek yapacağı Kaynağı ekler
        $dataMerge['dataSource'] = ['store' => 'dataGridS_'.$this->dataSourceName];

        // Ayarları json'a çevirir
        $jsonData = json_encode($dataMerge, JSON_PRETTY_PRINT);

        // Jsonda çıkacak hataları düzeltir.
        $jsonData = str_replace(': "'.$this->dataSourceName.'"', ':'.$this->dataSourceName, $jsonData);

        // Array içerisinde js fonksiyonu yazmışsak bu fonksiyonun önündeki ve arkasındaki tırnak işaretlerini kaldırarak fonksiyon haline gelmesini sağlar.
        // !!!!Dikkat function yazarken function den sonra bir boşluk olmaması gerekir. Örneğin: "function() {}"
        $re       = '/"(function\(.*?)"/mU';
        $jsonData = preg_replace($re, '$1', $jsonData);
        $jsonData = str_replace(['\n', '\t'], '', $jsonData);
        $jsonData = str_replace(['\"'], '"', $jsonData);
        $jsonData = str_replace(['\ln'], '\n', $jsonData);
        $jsonData = str_replace('"dataGridS_'.$this->dataSourceName.'"', 'dataGridS_'.$this->dataSourceName, $jsonData);

        // DataGrid için Script kodlarını hazırlar
        $jsConvert .= '<script>';
        $jsConvert .= $this->generateDataSource();
        $jsConvert .= 'var dataGrid = $("'.$this->selectorElement.'").dxDataGrid(';
        $jsConvert .= $jsonData;
        $jsConvert .= ').dxDataGrid(\'instance\');';
        $jsConvert .= 'function isNotEmpty(value) { return value !== undefined && value !== null && value !== ""; }';
        $jsConvert .= '</script>';

        return $jsConvert;
    }

    /**
     * DataGrid e button ekler.
     * $action değişkenine js fonksiyonu yazılabilir veya direk olarak url verilebilir.
     * $actionData değişkeni get parametresi olarak dönüştürülür. value olarak kolon adları verilebilir.
     *
     * @param $text
     * @param null $icon
     * @param string $action
     * @param array $actionData
     * @param string $cssClass
     * @param string $confrim
     * @return $this
     */
    public function addButton($text, $icon = NULL, $action = '', $actionData = [], $cssClass = '', $confrim = NULL)
    {
        // Daha önceden button eklenmemişse buton oluştur
        if (!isset($this->columns['action']['buttons'])) {
            $this->columns['action'] = [
                'type'    => 'buttons',
                'caption' => __('İşlemler'),
                'buttons' => [],
                'width'   => 200,
            ];
        }
        // Eklenmiş butonları çek
        $buttons = $this->columns['action']['buttons'];

        // Düzenleme veya Silme butonu js içerisinde tanımlı olduğu için direk olarak çek
        if ($text === 'edit' || $text === 'delete') {
            $buttons[] = $text;
        } else {
            // Özel bir buton ekle

            if (!strstr($action, 'function(')) {
                if (is_array($actionData)) {
                    foreach ($actionData as $key => $value) {
                        $actionData[$key] = $key.'="+rowData.'.$value.'+"';
                    }
                    $appendParams = '?';
                    if (strstr($action, '?')) {
                        $appendParams = '&';
                    }
                } else {
                    $appendParams = '/';
                    $actionData   = ['"+rowData.'.$actionData.'+"'];
                }
                $actionUrl = $action.$appendParams.implode('&', $actionData);
                $confrimJs = '';
                if ($confrim) {
                    $confrimJs = 'if(!confirm("'.$confrim.'")){ return false; }';
                }
                $action = 'function(e) {
                            '.$confrimJs.'
                            var rowData = e.row.data;
                            window.location.href = "'.$actionUrl.'"; 
                        }';
            }

            $buttons[] = [
                'hint'     => $text,
                'text'     => $text,
                'icon'     => $icon,
                'onClick'  => $action,
                'cssClass' => $cssClass,
            ];
        }

        $this->columns['action']['buttons'] = $buttons;

        return $this;
    }

    /**
     * Eklenen formları datagrid için ayarlar
     *
     * @return bool
     */
    private function formRender()
    {
        if (!$this->forms) {

            return false;
        }

        if (!isset($this->options['editing']['form']['items'])) {
            $this->options['editing']['form']['items'] = [];
        }

        foreach ($this->forms as $form) {
            $this->options['editing']['form']['items'][] = $form;
        }

        return true;
    }

    /**
     * Listeleme Filtreleme Sıralama işleri için yazılmış js bölümünü oluşturur
     *
     * @return string
     */
    private function generateDataSource()
    {
        // id olarak kullanılacak field ı belirler
        $key = '';
        if ($this->dataSourceOptions['key']) {
            $key = 'key: "'.$this->dataSourceOptions['key'].'",';
        }

        // Başarılı olduğunda hook atmak istersek
        $successCallback = '';
        if ($this->dataSourceOptions['successCallback']) {
            $successCallback = $this->dataSourceOptions['successCallback'].'(result,loadOptions);';
        }

        // Hatalı olduğunda hook atmak istersek
        $errorCallback = '';
        if ($this->dataSourceOptions['errorCallback']) {
            $errorCallback = $this->dataSourceOptions['errorCallback'].'(jqXHR, textStatus, errorThrown);';
        }

        // CRUD Ekleme işlemi için hook atmak istersek
        $insertCallback = '';
        if ($this->dataSourceOptions['insertCallback']) {
            $insertCallback = $this->dataSourceOptions['insertCallback'].'(values);';
        }

        // CRUD Güncelleme işlemi için hook atmak istersek
        $updateCallback = '';
        if ($this->dataSourceOptions['updateCallback']) {
            $updateCallback = $this->dataSourceOptions['updateCallback'].'(key, values);';
        }

        // CRUD Silme işlemi için hook atmak istersek
        $deleteCallback = '';
        if ($this->dataSourceOptions['deleteCallback']) {
            $deleteCallback = $this->dataSourceOptions['deleteCallback'].'(key);';
        }

        // Kaydetmek için ajax isteği yapacağı link varsa js fonksiyonu ile post et
        $insert = '';
        if ($insertUrl = $this->dataSourceOptions['insertUrl']) {
            //$insert = '$.post( "'.$insertUrl.'",{values:values});';
            $insert = '$.ajax({data: {values:values},url: "'.$insertUrl.'",type: "POST",dataType: "json"})';
        }

        // Düzenlemek için ajax isteği yapacağı link varsa js fonksiyonu ile post et
        $update = '';
        if ($updateUrl = $this->dataSourceOptions['updateUrl']) {
            //$update = '$.post( "'.$updateUrl.'",{key:key,values:values});';
            $update = '$.ajax({data: {key:key,values:values},url: "'.$updateUrl.'",type: "PUT",dataType: "json"})';
        }

        // Silmek için ajax isteği yapacağı link varsa js fonksiyonu ile post et
        $remove = '';
        if ($removeUrl = $this->dataSourceOptions['removeUrl']) {
            //$remove = '$.post( "'.$removeUrl.'",{key:key});';
            $remove = '$.ajax({data: {key:key},url: "'.$removeUrl.'",type: "DELETE",dataType: "json"})';
        }

        // Tanımlamaları js olarak düzenle
        return '
			var '.$this->dataSourceName.' = {
                '.$key.'        
                 insert: function (values) {
                    '.$insert.'
                    '.$insertCallback.'
			    },
			    update: function (key, values) {
			        '.$update.'
			        '.$updateCallback.'
			    },
			    remove: function (key) {
			        '.$remove.'
			        '.$deleteCallback.'
			    },
			    ajaxUrl: "'.$this->dataSourceOptions['url'].'",
			    load: function(loadOptions) {
                    var d = $.Deferred(),
                            params = {};
                    [
                        "skip",
                        "take",
                        "requireTotalCount",
                        "requireGroupCount",
                        "sort",
                        "filter",
                        "totalSummary",
                        "group",
                        "groupSummary"
                    ].forEach(function(i) {
                        if(i in loadOptions && isNotEmpty(loadOptions[i])) 
                            params[i] = JSON.stringify(loadOptions[i]);
                    });
                    
                    params.page = parseFloat(params.skip / params.take + 1);
			        if (isNaN(params.page)) {
			            params.page = 1;
			        }
                    
                    $.ajax({
			            url: '.$this->dataSourceName.'.ajaxUrl,
			            headers: {"X-Requested-With": "XMLHttpRequest"},
			            dataType: "json",
			            data: params,
			            success: function (result) {
			                var totalCount = 0;
			                if (typeof result.totalCount !== "undefined") {
			                    totalCount = result.totalCount;
			                } else {
			                    totalCount = result.total;
			                }
			                var groupCount = 0;
			                if (typeof result.groupCount !== "undefined") {
			                    var groupCount = result.groupCount;
			                }
			                
			                d.resolve(result.data, { 
                                totalCount: totalCount,
                                summary: result.summary,
                                groupCount: groupCount
                            });
			                '.$successCallback.'
			            },
			            error: function (jqXHR, textStatus, errorThrown) {
			                d.reject("Data Loading Error");
			                '.$errorCallback.'
			            },
			            timeout: '.$this->dataSourceOptions['timeout'].'
			        });
			
					var selectionFired = new CustomEvent("dxDataGridLoad", {"data": params });
				    document.dispatchEvent(selectionFired);
                    return d.promise();
                }
			};
			
			var dataGridS_'.$this->dataSourceName.' =  new DevExpress.data.CustomStore('.$this->dataSourceName.');
			';
    }

    /**
     * dxDataGrid ayarları Varsayılan olarak tanımlanmış değerlerin üzerine yazar
     *
     * @param array $options
     * @return $this
     */
    public function setOptions($options = [])
    {
        $this->options                               = array_merge($this->options, $options);
        $this->options['stateStoring']['storageKey'] = 'local_'.$this->dataSourceName;

        return $this;
    }

    /**
     * Ekleme ve Düzenleme için Popup için bir başlık belirler
     *
     * @param $title
     * @return $this
     */
    public function setPopupTitle($title)
    {
        $this->options['editing']['popup']['title']     = $title;
        $this->options['editing']['popup']['showTitle'] = true;

        return $this;
    }

    /**
     * Verileri çekmek için ajax isteği yapılacak url
     *
     * @param $url
     * @return $this
     */
    public function setAjaxUrl($url)
    {
        $this->setSourceOptions(['url' => $url]);

        return $this;
    }

    /**
     * Js tarafında yapılacak işlemleri belirler.
     * Varsayılan olarak tanımlanmış değerlerin üzerine yazar
     *
     * @param array $option
     * @return $this
     */
    public function setSourceOptions($option = [])
    {
        $this->dataSourceOptions = array_merge($this->dataSourceOptions, $option);

        return $this;
    }

    /**
     * Yeni kayıt eklemek için istek yapılacak url
     *
     * @param $url
     * @return $this
     */
    public function setInsertUrl($url)
    {
        $this->setSourceOptions(['insertUrl' => $url]);

        return $this;
    }

    /**
     * Kayıt güncellemek için istek yapılacak url
     *
     * @param $url
     * @return $this
     */
    public function setUpdateUrl($url)
    {
        $this->setSourceOptions(['updateUrl' => $url]);

        return $this;
    }

    /**
     * Kayıt silmek için istek yapılacak url
     *
     * @param $url
     * @return $this
     */
    public function setRemoveUrl($url)
    {
        $this->setSourceOptions(['removeUrl' => $url]);

        return $this;
    }

    /**
     * Özet verilerini ayarlar
     *
     * @param $column
     * @param $summaryType
     * @param null $showInColumn
     * @param string $alignment "left | center | right"
     * @param array $options
     * @return $this
     */
    public function setSummaryGroup($column, $summaryType, $showInColumn = NULL, $alignment = 'left', $options = [])
    {
        $this->setSummary($column, $summaryType, $showInColumn, $alignment, 'groupItems', $options);

        return $this;
    }

    /**
     * Özet verilerini ayarlar
     *
     * @param $column
     * @param $summaryType
     * @param null $showInColumn
     * @param string $alignment "left | center | right"
     * @param string $type "totalItems | groupItems"
     * @param array $options
     * @return $this
     */
    public function setSummary(
        $column,
        $summaryType,
        $showInColumn = NULL,
        $alignment = 'left',
        $type = 'totalItems',
        $options = []
    ) {
        if (!isset($this->options['summary'][$type])) {
            $this->options['summary'][$type] = [];
        }
        $this->options['summary'][$type][] = array_merge([
            'column'       => $column,
            'summaryType'  => $summaryType,
            'showInColumn' => $showInColumn ? $showInColumn : $column,
            'alignment'    => $alignment,
        ], $options);

        return $this;
    }

    /**
     * Sayfalamayı Ayarla
     *
     * @param int $pageSize
     * @param array $allowedPageSizes
     * @return $this
     */
    public function setPagination($pageSize = 50, $allowedPageSizes = [10, 50, 100, 500, 100])
    {
        $this->pageSize                      = $pageSize;
        $this->options['paging']['pageSize'] = $this->pageSize;
        $this->options['pager']              = [
            'showPageSizeSelector' => true,
            'showInfo'             => true,
            'allowedPageSizes'     => $allowedPageSizes,
        ];

        return $this;
    }

    /**
     * Tema Ayarla
     * Datagridin tasarımını değiştirmeye yarar.
     *
     * @link https://js.devexpress.com/Documentation/Guide/Themes_and_Styles/Predefined_Themes/
     *
     * @param $theme
     * @return $this
     */
    public function setTheme($theme)
    {
        // Kullanılabilecek Temalar
        /**
         * dx.light.css
         * dx.dark.css
         * dx.carmine.css
         * dx.softblue.css
         * dx.darkmoon.css
         * dx.darkviolet.css
         * dx.greenmist.css
         * dx.contrast.css
         * dx.light.compact.css
         * dx.dark.compact.css
         * dx.carmine.compact.css
         * dx.softblue.compact.css
         * dx.darkmoon.compact.css
         * dx.darkviolet.compact.css
         * dx.greenmist.compact.css
         * dx.contrast.compact.css
         * dx.material.blue.light.css
         * dx.material.blue.dark.css
         * dx.material.lime.light.css
         * dx.material.lime.dark.css
         * dx.material.orange.light.css
         * dx.material.orange.dark.css
         * dx.material.purple.light.css
         * dx.material.purple.dark.css
         * dx.material.teal.light.css
         * dx.material.teal.dark.css
         * dx.material.blue.light.compact.css
         * dx.material.blue.dark.compact.css
         * dx.material.lime.light.compact.css
         * dx.material.lime.dark.compact.css
         * dx.material.orange.light.compact.css
         * dx.material.orange.dark.compact.css
         * dx.material.purple.light.compact.css
         * dx.material.purple.dark.compact.css
         * dx.material.teal.light.compact.css
         * dx.material.teal.dark.compact.css
         */
        $this->themeCss = $theme;

        return $this;
    }

    /**
     * Yeni sütunu dropdown olarak ekler
     *
     * @link https://js.devexpress.com/Documentation/Guide/Widgets/DataGrid/Columns/Overview/
     *
     * @param string $dataField
     * @param string $caption
     * @param array $dataSource
     * @param array $column
     * @return $this
     */
    public function addColumnDropdown($dataField = '', $caption = '', $dataSource = [], $column = [])
    {
        // id,name olarak değiştir
        $dataSource = collect($dataSource)->mapToGroups(function($item, $key) {
            return [
                [
                    'id'   => $item ? $key : '(Null)',
                    'name' => $item ? $item : __('--Boş--'),
                ],
            ];
        })->flatten(1)->toArray();

        $column = array_merge($column, [
            'lookup' => [
                'valueExpr'   => 'id',
                'displayExpr' => 'name',
                'dataSource'  => $dataSource,
            ],
        ]);

        $this->addColumn($dataField, $caption, $column);

        return $this;
    }

    /**
     * Yeni sütun ekler
     *
     * @link https://js.devexpress.com/Documentation/Guide/Widgets/DataGrid/Columns/Overview/
     *
     * @param string $dataField
     * @param string $caption
     * @param array $column
     * @return $this
     */
    public function addColumn($dataField = '', $caption = '', $column = [])
    {
        if (is_array($caption) && !$column) {
            $column  = $caption;
            $caption = isset($column['caption']) ? $column['caption'] : '';
        }
        if (!$dataField && isset($column['dataField'])) {
            $dataField = $column['dataField'];
        } else {
            $column['dataField'] = $dataField;
        }

        $column['caption'] = $caption;

        $this->columns[$dataField] = array_merge($this->defaultColumns, $column);

        return $this;
    }

    /**
     * Yeni sütunu Colorbox olarak ekler
     *
     * @link https://js.devexpress.com/Documentation/Guide/Widgets/DataGrid/Columns/Overview/
     *
     * @param string $dataField
     * @param string $caption
     * @param array $column
     * @return $this
     */
    public function addColumnColor($dataField = '', $caption = '', $column = [])
    {
        $column = array_merge($column, [
            'cellTemplate'     => 'function(cellElement, cellInfo) { if (cellInfo.rowType !== "data") { return; } var color = cellInfo.value; cellElement.css("background-color", color); }',
            'editCellTemplate' => 'function(cellElement, cellInfo) { if (cellInfo.rowType !== "data") { return; } var color = cellInfo.value; $("<div>").dxColorBox({ value: color, onValueChanged: function(args) { cellInfo.setValue(args.value); } }).appendTo(cellElement); }',
        ]);

        $this->addColumn($dataField, $caption, $column);

        return $this;
    }

    /**
     * Yeni sütunu Textarea olarak ekler
     *
     * @link https://js.devexpress.com/Documentation/Guide/Widgets/DataGrid/Columns/Overview/
     *
     * @param string $dataField
     * @param string $caption
     * @param array $column
     * @return $this
     */
    public function addColumnTextArea($dataField = '', $caption = '', $column = [], $editorOptions = [])
    {
        $editorOptions = array_merge([
            'height' => 190,

        ], $editorOptions);

        $column = array_merge($column, [
            'dataType' => '',
            'formItem' => [
                'colSpan'       => 2,
                'editorType'    => 'dxTextArea',
                'editorOptions' => $editorOptions,
            ],
        ]);

        $this->addColumn($dataField, $caption, $column);

        return $this;
    }

    /**
     * MasterDetail eklemek için kullanılır.
     * Urlye istek atarak çıkan sonucu ekler veya js fonksiyonu ile daha önceden gelmiş veriler işlenerek kullanılabilir.
     *
     * @param $templateOrUrl
     * @param array $actionData
     * @return $this
     */
    public function setMasterDetail($templateOrUrl, $actionData = [])
    {
        $template = $templateOrUrl;
        // MasterDetail'i aktif et
        $this->options['masterDetail']['enabled'] = true;

        // Eğer js fonksiyonu değilse Url ye istek atacak şekilde ayarla
        if (!strstr($templateOrUrl, 'function(')) {

            $actionDataJs = '';

            if ($actionData) {
                // Array olarak gelen veriyi valularını rowdata ile birleştirir
                foreach ($actionData as $key => $value) {
                    $actionData[$key] = $key.': rowData.'.$value.', ';
                }
                // Json objesine çevir
                $actionData   = implode(' ', $actionData);
                $actionDataJs = 'data: { '.$actionData.' }';
            }
            // ajax ile istek atarak dönen sonucu ekrana bas
            $template = 'function(container, options) {
                 var rowData = options.data;
                 $.ajax({
                    type: "GET",
                    url: "'.$templateOrUrl.'",
                    '.$actionDataJs.'
                 }).done(function( template ) {
                    container.append(template);
                 });
            }';
        }

        $this->options['masterDetail']['template'] = $template;

        return $this;

    }

    /**
     * Form a field eklemek için kullanılır.
     *
     * @param $field
     * @param null $label
     * @param null $helpText
     * @param array $rules
     * @param null $type #'dxAutocomplete' | 'dxCalendar' | 'dxCheckBox' | 'dxColorBox' | 'dxDateBox' | 'dxDropDownBox' | 'dxLookup' | 'dxNumberBox' | 'dxRadioGroup' | 'dxRangeSlider' | 'dxSelectBox' | 'dxSlider' | 'dxSwitch' | 'dxTagBox' | 'dxTextArea' | 'dxTextBox'
     * @param array $options
     * @param null $colSpan
     * @param null $group
     * @return $this
     */
    public function addFormItem(
        $field,
        $label = NULL,
        $helpText = NULL,
        $rules = [],
        $type = NULL,
        $options = [],
        $colSpan = NULL,
        $group = NULL
    ) {
        // Eğer bir grup eklenmemişse son eklenen grubu getir
        if (!$group) {
            end($this->forms);
            $group = key($this->forms);
        }

        // Grup yoksa yeni bir grup oluştur
        if (!isset($this->forms[$group])) {
            $this->setFormGroup($group);
            $group = 0;
        }

        $item = [
            'dataField'       => $field,
            'editorType'      => $type,
            'helpText'        => $helpText,
            'label'           => ['text' => $label],
            'editorOptions'   => $options,
            'validationRules' => $rules,
            'colSpan'         => $colSpan,
        ];

        $this->setFormItem($item, $group);

        return $this;
    }

    /**
     * Formları gruplamak için kullanılır.
     *
     * @param null $caption
     * @param int $colCount
     * @param int $colSpan
     * @return $this
     */
    public function setFormGroup($caption = NULL, $colCount = 2, $colSpan = 2)
    {
        $formIndex = $caption;
        if (!$formIndex) {
            $formIndex = count($this->forms);
        }

        $this->forms[$formIndex] = [
            'itemType' => 'group',
            'colCount' => $colCount,
            'colSpan'  => $colSpan,
            'caption'  => $caption,
            'items'    => [],
        ];

        return $this;
    }

    /**
     * $this->forms bölümüne ekleme yapar ve $this->columns da yoksa formda görünebilmesi için ekler
     *
     * @param $item
     * @param $group
     */
    private function setFormItem($item, $group)
    {
        // Eğer Colum eklenmemişse formda görünecek şekilde ekle
        if (!isset($this->columns[$item['dataField']])) {
            $this->columns[$item['dataField']] = [
                'dataField' => $item['dataField'],
                'caption'   => $item['label']['text'],
                'visible'   => false,
            ];
        }
        $this->forms[$group]['items'][] = $item;
    }
}

/**
 * Boş arrayleri temizler
 */
if (!function_exists('array_remove_empty')) {
    function array_remove_empty($haystack)
    {
        foreach ($haystack as $key => $value) {
            if (is_array($value)) {
                $haystack[$key] = array_remove_empty($haystack[$key]);
            }

            if (empty($haystack[$key]) && $haystack[$key] !== false) {
                unset($haystack[$key]);
            }
        }

        return $haystack;
    }
}
