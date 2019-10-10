/**
 * @license Copyright (c) 2003-2017, CKSource - Frederico Knabben. All rights reserved.
 * For licensing, see LICENSE.md or http://ckeditor.com/license
 */
CKEDITOR.replaceClass = 'ckeditor';
CKEDITOR.editorConfig = function (config) {

    //config.extraPlugins = 'jsplusInclude,jsplusBootstrapEditor,jsplusBootstrapTableTools,jsplusBootstrapTools';
    config.language = 'tr-TR';
    config.toolbar = [
        ['PasteText', 'PasteFromWord', '-', 'Undo', 'Redo'],
        ['Find', 'Replace', '-', 'SelectAll', '-', 'Scayt'],
        ['Form', 'Checkbox', 'Radio', 'TextField', 'Textarea', 'Select', 'Button', 'ImageButton', 'HiddenField'],
        ['Bold', 'Italic', 'Underline', 'Strike', 'Subscript', 'Superscript', '-', 'CopyFormatting', 'RemoveFormat'],
        ['NumberedList', 'BulletedList', '-', 'Outdent', 'Indent', '-', 'Blockquote', '-', 'JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock', '-'],
        ['Link', 'Unlink',],
        ['Image', 'Table', 'HorizontalRule', 'Smiley', 'SpecialChar', 'PageBreak', 'Iframe'],
        ['Styles', 'Format', 'Font', 'FontSize'],
        ['Maximize', 'ShowBlocks'],
      /*  [
            'jsplusBootstrapEditor',
            'jsplusBootstrapEditorSelected'
        ],
        [
            'jsplus_bootstrap_icons',
            'jsplus_bootstrap_label',
            'jsplus_bootstrap_badge',
            'jsplus_bootstrap_alert',
        ],
        [
            'jsplusShowBlocks',
            '-',
            'jsplusBootstrapToolsContainerEdit',
            'jsplusBootstrapToolsContainerAdd',
            'jsplusBootstrapToolsContainerAddBefore',
            'jsplusBootstrapToolsContainerAddAfter',
            'jsplusBootstrapToolsContainerDelete',
            'jsplusBootstrapToolsContainerMoveUp',
            'jsplusBootstrapToolsContainerMoveDown',
            '-',
            'jsplusBootstrapToolsRowEdit',
            'jsplusBootstrapToolsRowAdd',
            'jsplusBootstrapToolsRowAddBefore',
            'jsplusBootstrapToolsRowAddAfter',
            'jsplusBootstrapToolsRowDelete',
            'jsplusBootstrapToolsRowMoveUp',
            'jsplusBootstrapToolsRowMoveDown',
            '-',
            'jsplusBootstrapToolsColEdit',
            'jsplusBootstrapToolsColAdd',
            'jsplusBootstrapToolsColAddBefore',
            'jsplusBootstrapToolsColAddAfter',
            'jsplusBootstrapToolsColDelete',
            'jsplusBootstrapToolsColMoveLeft',
            'jsplusBootstrapToolsColMoveRight',
        ],
        '/',
        [
            'jsplusTableAdd',
            'jsplusTableDelete',
            '-',
            'jsplusTableConf',
            '-',
            'jsplusTableRowAddBefore',
            'jsplusTableRowAddAfter',
            'jsplusTableRowConf',
            'jsplusTableRowMoveUp',
            'jsplusTableRowMoveDown',
            'jsplusTableRowDelete',
            '-',
            'jsplusTableColAddBefore',
            'jsplusTableColAddAfter',
            'jsplusTableColConf',
            'jsplusTableColMoveLeft',
            'jsplusTableColMoveRight',
            'jsplusTableColDelete',
            '-',
            'jsplusTableCellConf',
            'jsplusTableCellMergeRight',
            'jsplusTableCellMergeDown',
            'jsplusTableCellSplit'
        ],*/
        [
            'Source'
        ]
    ];

    config.removeButtons = 'NewPage,Preview,Print,Save,Form,Checkbox,Radio,TextField,Textarea,Select,Button,ImageButton,HiddenField,CreateDiv,Flash,Smiley,Font,About,SelectAll,CopyFormatting,Language,Templates';
    config.filebrowserImageBrowseUrl = window.mediaManagerUrl + '?type=TYPE_IMAGE';
    config.filebrowserBrowseUrl = window.mediaManagerUrl + '?type=TYPE_PDF,TYPE_DOCUMENT';
    config.allowedContent = true;
    config.autoGrow_onStartup = true;
    config.entities = false;

    config.enterMode = CKEDITOR.ENTER_P;
    config.shiftEnterMode = CKEDITOR.ENTER_BR;
    config.allowedContent = true;
    config.height = 600;
    config.skin = 'be';

    config.jsplusInclude = {
        framework: "b4"
    }
};
