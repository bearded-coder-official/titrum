<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ru">
<head>
    <meta http-equiv="Content-Type" content="text/html;charset=UTF-8"/>
    <title></title>

    <link rel="stylesheet" href="//netdna.bootstrapcdn.com/bootstrap/3.0.2/css/bootstrap.min.css"/>
    <link rel="stylesheet" href="http://bootswatch.com/united/bootstrap.min.css"/>

    <script type="text/javascript" src="//code.jquery.com/jquery-1.10.2.min.js"></script>
    <script type="text/javascript">
        $(document).ready(function(){
            var currentDiv = $(".step:first");
            currentDiv.show();

            $(".next").on("click", function(){
                currentDiv.hide();

                currentDiv = currentDiv.next(".step");
                currentDiv.show();
            });

            $(".prev").on("click", function(){
                currentDiv.hide();

                currentDiv = currentDiv.prev(".step");
                currentDiv.show();
            });

            var elementHtml = '<div class="element"><h3 class="text-center">Элемент #{{num}}</h3><div class="form-group col-sm-12"><label>HTML код:</label><textarea name="html" class="form-control" rows="5"></textarea><h3>Установки</h3><div class="row"><div class="col-sm-6"><h4>Показать:</h4><div class="form-group col-sm-6"><label>Минуты:</label><input type="text" class="form-control" name="start-minutes" /></div><div class="form-group col-sm-6"><label>Секунды:</label><input type="text" class="form-control" name="start-seconds" /></div></div><div class="col-sm-6"><h4>Скрыть:</h4><div class="form-group col-sm-6"><label>Минуты:</label><input type="text" class="form-control" name="end-minutes" /></div><div class="form-group col-sm-6"><label>Секунды:</label><input type="text" class="form-control" name="end-seconds" /></div></div></div><div class="row"><div class="col-sm-12"><div class="form-group"><button type="button" class="delete-element btn btn-danger">Удалить</button></div></div></div></div></div>';

            var fields = {
                'jw-player' : ['video-url', 'image-url'],
                'flow-player' : ['video-url', 'image-url'],
                'vimeo-player' : ['video-url'],
                'youtube-player' : ['video-id']
            };

            var additionalFieds = {
                'jw-player' : ['auto-play', 'hide-controls'],
                'flow-player' : ['auto-play', 'hide-controls'],
                'vimeo-player' : ['auto-play'],
                'youtube-player' : ['auto-play', 'hide-controls']
            };

            $("#add-element").click(function(){
                $("#elements").append(elementHtml.replace('{{num}}', $(".element").length+1));
            });

            $("div").on('click', '.delete-element', function(){
                if (confirm('Вы действительно хотите удалить этот элемент?')) {
                    $(this).closest('.element').remove();
                } else {
                    return false;
                }
            });

            $("input[name='player-type']").change(function(){
                $("#textfields input").closest(".form-group").hide();

                for (i = 0; i < fields[$("input[name=player-type]:checked").val()].length; i++) {
                    $("input[name='" + fields[$("input[name=player-type]:checked").val()][i] + "']").closest(".form-group").show();
                }
            });

            $("input[name='player-type']").change(function(){
                $("#controls label").hide();

                for (i = 0; i < additionalFieds[$("input[name=player-type]:checked").val()].length; i++) {
                    $("input[name='" + additionalFieds[$("input[name=player-type]:checked").val()][i] + "']").closest("label").show();

                }
            });

            $("#generate").click(function(){
                var settings = {};

                settings.player = $("input[name=player-type]:checked").val();

                if (settings.player == 'youtube-player') {
                    settings.videoUrl = "http://www.youtube.com/v/" + $("input[name=video-id]").val();
                    settings.imageUrl = "";
                } else {
                    settings.videoUrl = $("input[name=video-url]").val();
                    settings.imageUrl = $("input[name=image-url]").val();
                }

                settings.width = $("input[name=video-width]").val();
                settings.height = $("input[name=video-height]").val();
                settings.autoPlay = $("input[name=auto-play]").prop('checked') ? true : false;
                settings.hideControls = $("input[name=hide-controls]").prop('checked') ? true : false;
                settings.id = new Date().getUTCDay() + new Date().getUTCMilliseconds();

                var slides = [];
                var index = 0;
                $(".element").each(function() {
                    temp = {};
                    temp.id = "block-" + new Date().getUTCMilliseconds() + index;
                    temp.html = $(this).find("textarea[name=html]").val();
                    temp.startTime = parseInt($(this).find("input[name=start-minutes]").val()) * 60 + parseInt($(this).find("input[name=start-seconds]").val());
                    temp.endTime = parseInt($(this).find("input[name=end-minutes]").val()) * 60 + parseInt($(this).find("input[name=end-seconds]").val());

                    if (isNaN(temp.startTime)) temp.startTime = 0;
                    if (isNaN(temp.endTime)) temp.endTime = 99999999;

                    slides.push(temp);
                    index++;
                });

                settings.slides = slides;

                var html = "";

                html += "&lt;html&gt;\n\n";
                html += "&lt;head&gt;\n";
                html += "&lt;script type=\"text/javascript\" src=\"//ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js\"&gt;&lt;/script&gt;\n";
                html += "&lt;script type=\"text/javascript\" src=\"js/titrum.js\"&gt;&lt;/script&gt;\n";
                html += "&lt;script type=\"text/javascript\"&gt;\n";

                html += "    $(document).ready(function(){\n";

                html += "        var settings = {\n";
                html += "            id: 'player-" + settings.id + "',\n";
                html += "            player: '" + settings.player + "',\n";
                html += "            width: " + settings.width + ",\n";
                html += "            height: " + settings.height + ",\n";
                html += "            videoUrl: '" + settings.videoUrl + "',\n";
                html += "            imageUrl: '" + settings.imageUrl + "',\n";
                html += "            autoPlay: " + settings.autoPlay + ",\n";
                html += "            hideControls: " + settings.hideControls + "\n";
                html += "        };\n\n";

                html += "        var slides = [\n";
                for (i = 0; i < settings.slides.length; i++) {
                    html += "            {id: '" + settings.slides[i].id + "', start_time: " + settings.slides[i].startTime + ", end_time: " + settings.slides[i].endTime + "}"
                    html += (i != settings.slides.length-1) ? ",\n" : "\n";
                }
                html += "        ];\n\n";

                html += "        var titrum = new Titrum(settings, slides);\n";

                html += "    });\n";

                html += "&lt;/script&gt;\n";
                html += "&lt;/head&gt;\n\n";

                html += "&lt;body&gt;\n";

                html += "        &lt;center&gt;&lt;div id=\"player-" + settings.id + "\"&gt;&lt;/div&gt;&lt;/center&gt;\n";
                html += "        &lt;div id=\"slides-" + settings.id + "\"&gt;\n";
                for (i = 0; i < settings.slides.length; i++) {
                    html += "            &lt;div id=\"" + settings.slides[i].id + "\" style=\"display: none\"&gt;\n";
                    html += "                " + settings.slides[i].html + "\n";
                    html += "            &lt;/div&gt;\n";
                }
                html += "        &lt;/div&gt;\n";

                html += "&lt;/body&gt;\n";

                html += "&lt;/html&gt;\n";

                //$("#results").html("").append(html);

                $.post("test.php", { html: html })
                    .done(function( data ) {

                        currentDiv.hide();

                        currentDiv = currentDiv.next(".step");
                        currentDiv.show();

                        var link = "<a href=\"ex.php?id=" + data + "\" class=\"btn btn-success\">Скачать</a>";
                        $("#results").html("").append(link);

                });
            });
        });
    </script>

    <style type="text/css">
        .step {
            display: none;
        }
    </style>
</head>
<body>

<div class="container">
    <div class="row text-center">
        <h1>Titrum</h1>
    </div>

    <div id="player" class="panel panel-default">
        <div class="panel-body">

            <div class="row step">
                <div class="col-sm-12 text-center">
                    <h2>Выбор плеера</h2>
                </div>

                <div class="col-sm-12">
                    <div class="radio">
                        <label>
                            <input type="radio" name="player-type" value="jw-player" checked>
                            JW Player
                        </label>
                    </div>
                    <div class="radio">
                        <label>
                            <input type="radio" name="player-type" value="flow-player">
                            Flow Player
                        </label>
                    </div>
                    <div class="radio">
                        <label>
                            <input type="radio" name="player-type" value="vimeo-player">
                            Vimeo Player
                        </label>
                    </div>
                    <div class="radio">
                        <label>
                            <input type="radio" name="player-type" value="youtube-player">
                            YouTube
                        </label>
                    </div>
                </div>



                <div class="col-sm-12">
                    <div class="row">
                        <div class="col-sm-10"></div>
                        <div class="col-sm-2 text-right">
                            <button type="button" class="next btn btn-default">Далее</button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row step">
                <div class="row">
                    <div class="col-sm-12 text-center">
                        <h2>Опции отображения</h2>
                    </div>

                    <div class="col-sm-12">
                        <div class="form-group col-sm-4">
                            <label>Ширина:</label>
                            <input type="text" class="form-control" name="video-width" value="640" />
                        </div>

                        <div class="form-group col-sm-4">
                            <label>Высота:</label>
                            <input type="text" class="form-control" name="video-height" value="360" />
                        </div>

                        <div class="col-sm-4" id="controls">
                            <label class="checkbox">
                                <input type="checkbox" name="auto-play" checked /> автопроигрывание
                            </label>
                            <label class="checkbox">
                                <input type="checkbox" name="hide-controls" checked /> скрыть контроллеры
                            </label>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-xs-12">
                            <div class="col-xs-6 text-left">
                                <button type="button" class="prev btn btn-default">Назад</button>
                            </div>
                            <div class="col-xs-6 text-right">
                                <button type="button" class="next btn btn-default">Далее</button>
                            </div>
                    </div>
                </div>
            </div>

            <div class="row step">
                <div class="row">
                    <div class="col-sm-12 text-center">
                        <h2>Источник видео</h2>
                    </div>

                    <div id="textfields">
                        <div class="col-sm-12">
                            <div class="col-sm-12 form-group">
                                <label>Видео (URL):</label>
                                <input type="text" class="form-control" name="video-url" />
                            </div>

                            <div class="col-sm-12 form-group">
                                <label>Превью-изображение (URL):</label>
                                <input type="text" class="form-control" name="image-url" />
                            </div>

                            <div class="col-sm-12 form-group" style="display: none">
                                <label>Видео ID:</label>
                                <input type="text" class="form-control" name="video-id" />
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-xs-12">
                        <div class="col-xs-6 text-left">
                            <button type="button" class="prev btn btn-default">Назад</button>
                        </div>
                        <div class="col-xs-6 text-right">
                            <button type="button" class="next btn btn-default">Далее</button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row step">
                <div class="row">
                    <div class="col-sm-12 text-center">
                    </div>

                    <div class="col-sm-12">
                        <div id="elements">
                            <div class="element">
                                <h3 class="text-center">Элемент #1</h3>

                                <div class="form-group col-sm-12">

                                    <label>HTML код:</label>
                                    <textarea name="html" class="form-control" rows="5"></textarea>

                                    <h3>Установки</h3>

                                    <div class="row">

                                        <div class="col-sm-6">
                                            <h4>Показать:</h4>
                                            <div class="form-group col-sm-6">
                                                <label>Минуты:</label>
                                                <input type="text" class="form-control" name="start-minutes" />
                                            </div>

                                            <div class="form-group col-sm-6">
                                                <label>Секунды:</label>
                                                <input type="text" class="form-control" name="start-seconds" />
                                            </div>
                                        </div>

                                        <div class="col-sm-6">
                                            <h4>Скрыть:</h4>
                                            <div class="form-group col-sm-6">
                                                <label>Минуты:</label>
                                                <input type="text" class="form-control" name="end-minutes" />
                                            </div>

                                            <div class="form-group col-sm-6">
                                                <label>Секунды:</label>
                                                <input type="text" class="form-control" name="end-seconds" />
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-sm-12">
                                            <div class="form-group">
                                                <button type="button" class="delete-element btn btn-danger">Удалить</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-xs-12">
                        <div class="col-xs-12 text-right">
                            <button id="add-element" type="button" class="btn btn-default">Добавить новый элемент</button>
                        </div>
                    </div>
                </div>

                <br/>

                <div class="row">
                    <div class="col-xs-12">
                        <div class="col-xs-6 text-left">
                            <button type="button" class="prev btn btn-default">Назад</button>
                        </div>
                        <div class="col-xs-6 text-right">
                            <button id="generate" type="button" class="btn btn-primary">Генерировать</button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row step">
                <div class="row">
                    <div class="col-sm-12">
                        <ol>
                            <h2 class="text-center">Результат</h2>
                            <li>Скачайте и разархивируйте zip архив</li>
                            <li>Поместите файлы на локальный сервер или хостинг</li>
                        </ol>
                    </div>
                </div>

                <br/>

                <div class="row">
                    <div class="col-xs-12">
                        <div class="col-xs-6 text-left">
                            <button type="button" class="prev btn btn-default">Назад</button>
                        </div>
                        <div id="results" class="col-xs-6 text-right">

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>



</body>
</html>