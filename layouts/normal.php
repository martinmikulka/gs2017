<?php
defined('BWC_VALID_INCLUDE') or header('HTTP/1.1 401 Unauthorized') or die('401 Unauthorized');
global $BController, $BView;
?>
<!doctype html>
<!--[if IE]><html class="ie"><![endif]-->
<!--[if !IE] --><html class=""><!-- <![endif]-->
    <head>
        <meta http-equiv="content-type" content="text/html; charset=utf-8" />
        <meta charset="utf-8" />
        <meta name="viewport" content="width=1140">
        <meta name="description" content="<?php ehtml($BView->Params['description']); ?>" />
        <meta name="author" content="ProfiSMS s.r.o." />

        <title><?php ehtml($BView->Params['title']); ?></title>

        <link href="/assets/dist/css/gs2017.min.css" type="text/css" rel="stylesheet" />

        <script>
        (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
        (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
        m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
        })(window,document,'script','https://www.google-analytics.com/analytics.js','ga');
        ga('create', '<?php ehtml(GA) ?>', 'auto');
        ga('send', 'pageview');
        </script>

    </head>
    <body id="Top">
        <div id="MenuWaypoint"></div>

        <div id="Menu" class="static">
            <?php include ROOT_DIR . '/views/partial/menu_bar.php'; ?>
        </div>
        <div id="Content">
            <?php $BView->Process(BView::PROCESS_VIEW); ?>
        </div>

        <script src="/assets/dist/js/gs2017.min.js"></script>
        <script>
            var app;
            document.addEventListener("DOMContentLoaded", function() {
                app = new mmi.App();
            });
        </script>
    </body>    
</html>
