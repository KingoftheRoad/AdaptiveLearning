$(function() {
    ConvertToCkEditor.init();
});

// ConvertToCkEditor Jquery script
ConvertToCkEditor = {
    init: function() {
        var mathElements = [
            'math', 'maction', 'maligngroup', 'malignmark', 'menclose', 'merror',
            'mfenced', 'mfrac', 'mglyph', 'mi', 'mlabeledtr', 'mlongdiv', 'mmultiscripts',
            'mn', 'mo', 'mover', 'mpadded', 'mphantom', 'mroot', 'mrow', 'ms', 'mscarries',
            'mscarry', 'msgroup', 'msline', 'mspace', 'msqrt', 'msrow', 'mstack', 'mstyle',
            'msub', 'msup', 'msubsup', 'mtable', 'mtd', 'mtext', 'mtr', 'munder', 'munderover', 'semantics',
            'annotation', 'annotation-xml'
        ];

        // Remove CKeditor buttons
        //var removeButtons = 'Source,Save,NewPage,DocProps,Preview,Print,Templates,document,Cut,Copy,Paste,PasteText,PasteFromWord,Undo,Redo,Find,Replace,SelectAll,Scayt,Form,Checkbox,Radio,TextField,Textarea,Select,Button,ImageButton,HiddenField,Bold,Italic,Underline,Strike,Subscript,Superscript,RemoveFormat,NumberedList,BulletedList,Outdent,Indent,Blockquote,CreateDiv,JustifyLeft,JustifyCenter,JustifyRight,JustifyBlock,BidiLtr,BidiRtl,Link,Unlink,Anchor,CreatePlaceholder,Image,Flash,Table,HorizontalRule,Smiley,SpecialChar,PageBreak,Iframe,InsertPre,Styles,Format,Font,FontSize,TextColor,BGColor,UIColor,Maximize,ShowBlocks,button1,button2,button3,oembed,MediaEmbed,About';
        var removeButtons = 'Cut,Copy,Paste,Source,Save,NewPage,DocProps,Preview,Print,Templates,document,PasteText,PasteFromWord,Undo,Redo,Find,Replace,SelectAll,Scayt,Form,Checkbox,Radio,TextField,Textarea,Select,Button,HiddenField,Outdent,Indent,CreateDiv,JustifyLeft,JustifyCenter,JustifyRight,JustifyBlock,BidiLtr,BidiRtl,Link,Unlink,Anchor,CreatePlaceholder,Flash,Table,HorizontalRule,Smiley,SpecialChar,PageBreak,Iframe,InsertPre,BGColor,UIColor,Maximize,ShowBlocks,button1,button2,button3,oembed,About';


        //CKEDITOR.plugins.addExternal('ckeditor_wiris', 'https://ckeditor.com/docs/ckeditor4/4.16.1/examples/assets/plugins/ckeditor_wiris/', 'plugin.js');
        CKEDITOR.plugins.addExternal('ckeditor_wiris', BASE_URL+'/public/ckeditor_wiris/', 'plugin.js');

        // Set editor allignment for allowed content true
        CKEDITOR.config.allowedContent = true;
        CKEDITOR.config.wiriseditorparameters = {"fontSize":"20px","fontFamily":"Times New Roman"}

        var QuestionEditor = Array('question_en', 'question_ch');
        $.each(QuestionEditor, function (i, QuestionEditor) {
            CKEDITOR.replace(QuestionEditor, {
                removeButtons : removeButtons,
                extraPlugins: 'ckeditor_wiris',
                // For now, MathType is incompatible with CKEditor file upload plugins.
                filebrowserBrowseUrl: BASE_URL+'/public/ckfinder/ckfinder.html',
                filebrowserUploadUrl: BASE_URL+'/public/ckfinder/core/connector/php/connector.php?command=QuickUpload&type=Files',
                height: 200,
                // Update the ACF configuration with MathML syntax.
                extraAllowedContent: mathElements.join(' ') + '(*)[*]{*};img[data-mathml,data-custom-editor,role](Wirisformula)'
            });
        });


        var NameOfCkeditor = Array(
            'answer1_en','answer2_en','answer3_en','answer4_en',
            'answer1_ch','answer2_ch','answer3_ch','answer4_ch');
        $.each(NameOfCkeditor, function (i, NameOfCkeditor) {
            CKEDITOR.replace(NameOfCkeditor, {
                removeButtons : removeButtons,
                extraPlugins: 'ckeditor_wiris',
                // For now, MathType is incompatible with CKEditor file upload plugins.
                filebrowserBrowseUrl: BASE_URL+'/public/ckfinder/ckfinder.html',
                filebrowserUploadUrl: BASE_URL+'/public/ckfinder/core/connector/php/connector.php?command=QuickUpload&type=Files',
                height: 200,
                // Update the ACF configuration with MathML syntax.
                extraAllowedContent: mathElements.join(' ') + '(*)[*]{*};img[data-mathml,data-custom-editor,role](Wirisformula)'
            });
        });


        var GeneralHintsOfCkeditor = Array(
            'general_hints_en',
            'general_hints_ch',
            'full_solution_en',
            'full_solution_ch'
        );
        $.each(GeneralHintsOfCkeditor, function (i, GeneralHintsOfCkeditor) {
            CKEDITOR.replace(GeneralHintsOfCkeditor, {
                removeButtons : removeButtons,
                extraPlugins: 'ckeditor_wiris',
                // For now, MathType is incompatible with CKEditor file upload plugins.
                filebrowserBrowseUrl: BASE_URL+'/public/ckfinder/ckfinder.html',
                filebrowserUploadUrl: BASE_URL+'/public/ckfinder/core/connector/php/connector.php?command=QuickUpload&type=Files',
                height: 200,
                // Update the ACF configuration with MathML syntax.
                extraAllowedContent: mathElements.join(' ') + '(*)[*]{*};img[data-mathml,data-custom-editor,role](Wirisformula)'
            });
        });

        // All NameOfCkeditor to convert ckeditor
        // $.each(NameOfCkeditor, function (i, NameOfCkeditor) {
        //     CKEDITOR.replace(NameOfCkeditor, {
        //         extraPlugins: 'ckeditor_wiris',
        //         // For now, MathType is incompatible with CKEditor file upload plugins.
        //         removePlugins: 'uploadimage,uploadwidget,uploadfile,filetools,filebrowser',
        //         height: 200,
        //         // Update the ACF configuration with MathML syntax.
        //         extraAllowedContent: mathElements.join(' ') + '(*)[*]{*};img[data-mathml,data-custom-editor,role](Wirisformula)'
        //     });
        // });
        

        var NameofHints = Array(
            'hint_answer1_en','hint_answer2_en','hint_answer3_en','hint_answer4_en',
            'hint_answer1_ch','hint_answer2_ch','hint_answer3_ch','hint_answer4_ch',
            'node_hint_answer1_en','node_hint_answer2_en','node_hint_answer3_en','node_hint_answer4_en',
            'node_hint_answer1_ch','node_hint_answer2_ch','node_hint_answer3_ch','node_hint_answer4_ch'
        );
        
        //For Hint Height Manage
        $.each(NameofHints, function (i, NameOfCkeditor) {
            CKEDITOR.replace(NameOfCkeditor, {
            	removeButtons : removeButtons,
                extraPlugins: 'ckeditor_wiris',
                // For now, MathType is incompatible with CKEditor file upload plugins.
                filebrowserBrowseUrl: BASE_URL+'/public/ckfinder/ckfinder.html',
                filebrowserUploadUrl: BASE_URL+'/public/ckfinder/core/connector/php/connector.php?command=QuickUpload&type=Files',
                height: 120,
                // Update the ACF configuration with MathML syntax.
                extraAllowedContent: mathElements.join(' ') + '(*)[*]{*};img[data-mathml,data-custom-editor,role](Wirisformula)'
            });
        });
        
        // CKEDITOR.replace('editor1', {
        //     filebrowserBrowseUrl: '/ckfinder/ckfinder.html',
        //     filebrowserUploadUrl: '/ckfinder/core/connector/php/connector.php?command=QuickUpload&type=Files'
        // });
    }
};