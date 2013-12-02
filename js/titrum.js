function Titrum(settings, slides)
{
    var player = null;

    var container = settings.id;
    var slides = slides;

    var playerType = settings.player;
    var currentTime = null;

    if (playerType == 'youtube-player') {
        $.getScript("http://ajax.googleapis.com/ajax/libs/swfobject/2.2/swfobject.js").done(function() {
            var params = { allowScriptAccess: "always" };
            var atts = {};

            settings.videoUrl += "?enablejsapi=1";
            if (settings.autoPlay) settings.videoUrl += "&autoplay=1";
            if (settings.hideControls) settings.videoUrl += "&controls=0";

            swfobject.embedSWF(settings.videoUrl,
                container, settings.width, settings.height, "8", null, null, params, atts,
                function (e) {
                    if (e.success) {
                        initPlayer();
                    }
                }
            );
        });
    } else if (playerType == 'jw-player') {
        settings.hideControls = settings.hideControls == true ? "none" : "over";

        $.getScript("http://jwpsrv.com/library/YzAEZjsoEeOqTiIACqoGtw.js", function() {
            var options = {
                file: settings.videoUrl,
                width: settings.width,
                height: settings.height,
                primary: 'flash',
                autostart : settings.autoPlay,
                'controlbar.position': settings.hideControls,
                events: {
                    onPlay: initPlayer
                }
            };

            if (settings.imageUrl != "")
                options["image"] = settings.imageUrl;

            jwplayer(container).setup(options);
        });
    } else if (playerType == 'flow-player') {
        $("#" + container).css({"width" : settings.width + "px", "height" : settings.height + "px"});

        $.getScript("http://releases.flowplayer.org/js/flowplayer-3.2.12.min.js", function() {
            var options = {
                clip:  {
                    autoPlay: false,
                    onStart: initPlayer,
                    autoPlay: settings.autoPlay
                }
            };

            if (settings.hideControls) options['plugins'] = {controls: null};

            var playlist = [];
            if (settings.imageUrl != "")
                playlist.push({ url: settings.imageUrl, scaling: 'orig' });
            playlist.push({ url: settings.videoUrl });
            options['playlist'] = playlist;

            flowplayer(
                container,
                "http://releases.flowplayer.org/swf/flowplayer-3.2.16.swf",
                options
            );
        });
    } else if (playerType == 'vimeo-player') {
        $.getJSON('http://www.vimeo.com/api/oembed.json?url=' + encodeURIComponent('http://vimeo.com/' + settings.videoUrl) + '&width=' + settings.width + '&height=' + settings.height + '&autoplay=' + settings.hideControls + '&callback=?', function(data){
            $("#" + container).html(data.html);

            $.getScript('http://a.vimeocdn.com/js/froogaloop2.min.js', function(){
                initPlayer();
            });
        });
    }

    function initPlayer()
    {
        if (playerType == 'youtube-player') {
            player = document.getElementById(container);
        } else if (playerType == 'jw-player') {
            player = document.getElementById(container);
        } else if (playerType == 'flow-player') {
            player = document.getElementById(container);
        } else if (playerType == 'vimeo-player') {
            player = $f($("#" + container + " iframe")[0]);

            player.addEvent('playProgress', function(data, id){
                currentTime = data.seconds;
                for (i = 0; i < slides.length; i++) {
                    if (currentTime >= slides[i].start_time && currentTime <= slides[i].end_time) {
                        $("#" + slides[i].id).show(500);
                    } else {
                        $("#" + slides[i].id).hide(500);
                    }
                }
            });

            return '';
        }

        setInterval(function(){
            play();
        }, 500);
    }

    function play()
    {
        if (playerType == 'youtube-player') {
            currentTime = player.getCurrentTime();
        } else if (playerType == 'jw-player') {
            currentTime = jwplayer(container).getPosition();
        } else if (playerType == 'flow-player') {
            currentTime = flowplayer(container).getTime();
        }

        for (i = 0; i < slides.length; i++) {
            if (currentTime >= slides[i].start_time && currentTime <= slides[i].end_time) {
                $("#" + slides[i].id).show(500);
            } else {
                $("#" + slides[i].id).hide(500);
            }
        }
    }
}