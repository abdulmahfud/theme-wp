jQuery( document ).ready(function( $ ) {
var page = 1;
var loading = false;
var $win = $(window);

$win.on('scroll', function () {
    var $document = $(document);
    var $body = $('body');
    var scrollTop = $document.scrollTop();
    var documentHeight = $document.height();
    var windowHeight = $win.height();
    var scrollPercentage = (scrollTop / (documentHeight - windowHeight)) * 100;

    if (scrollPercentage > 50) {
        if (!loading) {
            loading = true;
            page++;

            $.ajax({
                url: '/wp-json/wp/v2/posts?page=' + page + '&per_page=2',
                beforeSend: function () {
                    $body.append('<div id="loader">Loading...</div>');
                },
                success: function (data) {
                    var $posts = $('#posts');
                    var html = '';

                    for (var i = 0; i < data.length; i++) {
                        html += '<h2><a href="' + data[i].link + '">' + data[i].title.rendered + '</a></h2>';
                        html += '<p>' + data[i].excerpt.rendered + '</p>';
                    }

                    $posts.append(html);
                    $('#loader').remove();
                    loading = false;
                }
            });
        }
    }
});
	
});