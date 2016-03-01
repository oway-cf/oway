<!DOCTYPE html>
<html lang="en" ng-app="oWay">
<head>
    <meta charset="UTF-8">
    <title>oWay - оптимизатор поездок по городу</title>
    <link rel="shortcut icon" href="./image/favicon.png" type="image/x-icon">
    <link rel="icon" href="./image/favicon.png" type="image/x-icon">
    <link href="//malihu.github.io/custom-scrollbar/jquery.mCustomScrollbar.min.css" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Roboto:400,300,300italic,100,100italic,400italic,500,500italic,700,700italic,900,900italic"
          rel="stylesheet" type="text/css">
    <link rel="stylesheet" href="css/styles.css">
</head>
<body ng-controller="MainController">
<header class="header">
    <div class="header__logo">
        <img class="header__logo-img" src="image/logo-white.png" alt="oWay">
        <!--<span class="header__logo-slogan">Slogan every body</span>-->
    </div>
    <div class="header__nav">
        <a class="header__nav-link" data-toggle="modal" data-target="#myModal">Поделиться</a>
        <a href="http://oway.cf/doc/" class="header__nav-link" target="_blank">Документация</a>
        <a href="http://oway.cf/android/OWay.apk" class="header__nav-link" target="_blank">Скачать</a>
        <a class="header__login-btn" data-toggle="modal" data-target="#warningModal">Войти</a>
    </div>
</header>

<left-form class="left-form" ng-style="{'height': height+'px'}"></left-form>
<gis-map points="ways" add-point="addItem(item)" coords="list.todo_list_items"></gis-map>

<a href="http://oway.cf/android/OWay.apk" target="_blank"><img class="download-box" src="./image/google-play.png"
                                                               alt="download"></a>


<div class="loader-wrapper" ng-show="wayBuilding">
    <div class="loader-route">
        <img class="pulse" src="image/load-icon.png"/>

        <p>Загружаем...</p>
    </div>
</div>

<div class="modal fade" tabindex="-1" role="dialog" id="warningModal" tabindex="-1" role="dialog"
     aria-labelledby="myModalLabel">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Ой, не переживайте!</h4>
            </div>
            <div class="modal-body">
                <div class="modal-text">Функционал пока не реализован</div>

                <img src="./image/warning.png" alt="">
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<div class="modal fade" tabindex="-1" role="dialog" id="myModal" tabindex="-1" role="dialog"
     aria-labelledby="myModalLabel">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Поделиться ссылкой</h4>
            </div>
            <div class="modal-body">
                <div class="modal-text">Скопируйте ссылку на ваш маршрут и поделитесь ей с друзьями для совместного
                    приключения
                </div>

                <img ng-src="http://api.qrserver.com/v1/create-qr-code/?color=000000&amp;bgcolor=FFFFFF&amp;data=http%3A%2F%2Foway.cf%2F%23%2F@{{list.id}}&amp;qzone=1&amp;margin=0&amp;size=400x400&amp;ecc=L"
                     alt="">

                <div class="modal-link">@{{'http://oway.cf/#/'+list.id}}</div>
            </div>
            <div class="modal-footer">
                <div class="pluso"
                     data-background="transparent"
                     data-options="big,round,line,horizontal,counter,theme=04"
                     data-services="vkontakte,odnoklassniki,facebook,twitter"
                     ng-attr-data-url="@{{'http://oway.cf/#/'+list.id}}"
                     ng-attr-data-title="oWay - Оптимизатор поездок по городу"
                     data-description="Оптимизатор поездок по городу"></div>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<!--Connection scripts-->

<script src="https://code.jquery.com/jquery-2.2.1.min.js"></script>
<script src="https://code.jquery.com/ui/1.11.4/jquery-ui.min.js"></script>
<script src="http://maps.api.2gis.ru/2.0/loader.js?pkg=full"></script>
<script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.5.0/angular.min.js"></script>
<script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.5.0/angular-resource.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/angular-ui-sortable/0.13.4/sortable.js"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.0/jquery.min.js"></script>
<script src="//malihu.github.io/custom-scrollbar/jquery.mCustomScrollbar.concat.min.js"></script>
<script src="/js/app.js"></script>
<script type="text/javascript">
    var SHARE_ID;
    (function () {
        if (window.pluso)if (typeof window.pluso.start == "function") return;
        if (window.ifpluso == undefined) {
            window.ifpluso = 1;
            var d = document, s = d.createElement('script'), g = 'getElementsByTagName';
            s.type = 'text/javascript';
            s.charset = 'UTF-8';
            s.async = true;
            s.src = ('https:' == window.location.protocol ? 'https' : 'http') + '://share.pluso.ru/pluso-like.js';
            var h = d[g]('body')[0];
            h.appendChild(s);
        }
    })();</script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"
        integrity="sha384-0mSbJDEHialfmuBBQP6A4Qrprq5OVfW37PRR3j5ELqxss1yVqOtnepnHVP9aJ7xS"
        crossorigin="anonymous"></script>

<script>
    $('#myModal').on('shown.bs.modal', function () {
        $('.pluso').data('url', 'http://oway.cf/#/' + SHARE_ID);
    })
</script>
</body>
</html>