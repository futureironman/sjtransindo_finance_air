tinymce.remove("#jonatan");

tinymce.init({
    selector: 'textarea#jonatan',
    relative_urls: false,
    force_br_newlines: true,
    force_p_newlines: false,
    forced_root_block: '',
    remove_script_host: true,
    document_base_url: "/",
    convert_urls: true,
    height: 350,
    menubar: false,
    theme: 'modern',
    plugins: [
        'advlist autolink lists link charmap hr anchor pagebreak',
        'searchreplace wordcount visualblocks visualchars code fullscreen',
        'insertdatetime media nonbreaking save table contextmenu directionality',
        'emoticons template paste textcolor colorpicker textpattern imagetools image'
    ],
    theme_advanced_fonts: "Andale Mono=andale mono,times;" +
        "Arial=arial,helvetica,sans-serif;" +
        "Arial Black=arial black,avant garde;" +
        "Book Antiqua=book antiqua,palatino;" +
        "Comic Sans MS=comic sans ms,sans-serif;" +
        "Courier New=courier new,courier;" +
        "Georgia=georgia,palatino;" +
        "Helvetica=helvetica;" +
        "Impact=impact,chicago;" +
        "Symbol=symbol;" +
        "Tahoma=tahoma,arial,helvetica,sans-serif;" +
        "Terminal=terminal,monaco;" +
        "Times New Roman=times new roman,times;" +
        "Trebuchet MS=trebuchet ms,geneva;" +
        "Verdana=verdana,geneva;" +
        "Webdings=webdings;" +
        "Wingdings=wingdings,zapf dingbats",
    toolbar1: 'styleselect | fontselect | fontsizeselect | bold italic | alignleft aligncenter alignright alignjustify | print preview media',
    toolbar2: 'link unlink cleanup code,|,forecolor,backcolor, table,advhr,|,sub,sup,|,fullscreen,|,bullist,numlist,outdent,indent,undo,redo',
    image_advtab: true,
    templates: [
        { title: 'Test template 1', content: 'Test 1' },
        { title: 'Test template 2', content: 'Test 2' }
    ],
    content_css: [
        '//fast.fonts.net/cssapi/e6dc9b99-64fe-4292-ad98-6974f93cd2a2.css',
        '//www.tinymce.com/css/codepen.min.css'
    ]
});

tinymce.remove("#jonatan2");

tinymce.init({
    selector: 'textarea#jonatan2',
    relative_urls: false,
    force_br_newlines: true,
    force_p_newlines: false,
    forced_root_block: '',
    remove_script_host: true,
    document_base_url: "/",
    convert_urls: true,
    height: 200,
    menubar: false,
    theme: 'modern',
    plugins: [
        'advlist autolink lists link charmap hr anchor pagebreak',
        'searchreplace wordcount visualblocks visualchars code fullscreen',
        'insertdatetime media nonbreaking save table contextmenu directionality',
        'emoticons template paste textcolor colorpicker textpattern imagetools image'
    ],
    theme_advanced_fonts: "Andale Mono=andale mono,times;" +
        "Arial=arial,helvetica,sans-serif;" +
        "Arial Black=arial black,avant garde;" +
        "Book Antiqua=book antiqua,palatino;" +
        "Comic Sans MS=comic sans ms,sans-serif;" +
        "Courier New=courier new,courier;" +
        "Georgia=georgia,palatino;" +
        "Helvetica=helvetica;" +
        "Impact=impact,chicago;" +
        "Symbol=symbol;" +
        "Tahoma=tahoma,arial,helvetica,sans-serif;" +
        "Terminal=terminal,monaco;" +
        "Times New Roman=times new roman,times;" +
        "Trebuchet MS=trebuchet ms,geneva;" +
        "Verdana=verdana,geneva;" +
        "Webdings=webdings;" +
        "Wingdings=wingdings,zapf dingbats",
    toolbar1: 'styleselect | fontselect | fontsizeselect | bold italic | alignleft aligncenter alignright alignjustify | print preview media',
    toolbar2: 'link unlink cleanup code,|,forecolor,backcolor, table,advhr,|,sub,sup,|,fullscreen,|,bullist,numlist,outdent,indent,undo,redo',
    image_advtab: true,
    templates: [
        { title: 'Test template 1', content: 'Test 1' },
        { title: 'Test template 2', content: 'Test 2' }
    ],
    content_css: [
        '//fast.fonts.net/cssapi/e6dc9b99-64fe-4292-ad98-6974f93cd2a2.css',
        '//www.tinymce.com/css/codepen.min.css'
    ],
    file_browser_callback: function openKCFinder(field_name, url, type, win) {
        tinyMCE.activeEditor.windowManager.open({
            file: '../kcfinder/browse.php?opener=tinymce4&field=' + field_name + '&type=' + type,
            title: 'KCFinder',
            width: 700,
            height: 500,
            resizable: "yes",
            inline: true,
            close_previous: "no",
            popup_css: false
        }, {
            window: win,
            input: field_name
        });
        return false;
    }
});

tinymce.remove("#jonatan3");

tinymce.init({
    selector: 'textarea#jonatan3',
    relative_urls: false,
    force_br_newlines: true,
    force_p_newlines: false,
    forced_root_block: '',
    remove_script_host: true,
    document_base_url: "/",
    convert_urls: true,
    height: 300,
    menubar: false,
    theme: 'modern',
    plugins: [
        'advlist autolink lists link charmap hr anchor pagebreak',
        'searchreplace wordcount visualblocks visualchars code fullscreen',
        'insertdatetime media nonbreaking save table contextmenu directionality',
        'emoticons template paste textcolor colorpicker textpattern imagetools image'
    ],
    theme_advanced_fonts: "Andale Mono=andale mono,times;" +
        "Arial=arial,helvetica,sans-serif;" +
        "Arial Black=arial black,avant garde;" +
        "Book Antiqua=book antiqua,palatino;" +
        "Comic Sans MS=comic sans ms,sans-serif;" +
        "Courier New=courier new,courier;" +
        "Georgia=georgia,palatino;" +
        "Helvetica=helvetica;" +
        "Impact=impact,chicago;" +
        "Symbol=symbol;" +
        "Tahoma=tahoma,arial,helvetica,sans-serif;" +
        "Terminal=terminal,monaco;" +
        "Times New Roman=times new roman,times;" +
        "Trebuchet MS=trebuchet ms,geneva;" +
        "Verdana=verdana,geneva;" +
        "Webdings=webdings;" +
        "Wingdings=wingdings,zapf dingbats",
    toolbar1: 'styleselect | fontselect | fontsizeselect | bold italic | alignleft aligncenter alignright alignjustify | print preview media',
    toolbar2: 'link image unlink cleanup code,|,forecolor,backcolor, table,advhr,|,sub,sup,|,fullscreen,|,bullist,numlist,outdent,indent,undo,redo',
    image_advtab: true,
    templates: [
        { title: 'Test template 1', content: 'Test 1' },
        { title: 'Test template 2', content: 'Test 2' }
    ],
    content_css: [
        '//fast.fonts.net/cssapi/e6dc9b99-64fe-4292-ad98-6974f93cd2a2.css',
        '//www.tinymce.com/css/codepen.min.css'
    ],
    file_browser_callback: function openKCFinder(field_name, url, type, win) {
        tinyMCE.activeEditor.windowManager.open({
            file: '../kcfinder/browse.php?opener=tinymce4&field=' + field_name + '&type=' + type,
            title: 'KCFinder',
            width: 700,
            height: 500,
            resizable: "yes",
            inline: true,
            close_previous: "no",
            popup_css: false
        }, {
            window: win,
            input: field_name
        });
        return false;
    }
});