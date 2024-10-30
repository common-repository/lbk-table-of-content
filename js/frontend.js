(function($){
    $(document).ready(function() {
        // var elem = document.querySelector('.lbk-toc');
        // var logViewport = function () {
        //     var isOut = isOutOfViewport(elem);

        //     if ( isOut.bottom ) {
        //         console.log('Meow');
        //     }
        // };

        // window.addEventListener('scroll', logViewport, false);

        $(document).on('click', 'a[href^="#"]', function (event) {
            $('html, body').animate({
                scrollTop: $($.attr(this, 'href')).offset().top
            }, 500);
        });

        $('.lbk-toc').on('click', '.lbk-toc-icon', function() {
            $(this).toggleClass('lbk-toc-icon-collapse');
            $(this).toggleClass('lbk-toc-icon-expand');

            $('+ol', this).toggle(500, "linear");
        });

        // function isOutOfViewport(elem) {
        //     // Get element's bounding
        //     var bounding = elem.getBoundingClientRect();

        //     // Check if it's out of the viewport on each side
        //     var out = {};
        //     out.top = bounding.top < 0;
        //     out.left = bounding.left < 0;
        //     out.bottom = bounding.bottom > (window.innerHeight || document.documentElement.clientHeight);
        //     out.right = bounding.right > (window.innerWidth || document.documentElement.clientWidth);
        //     out.any = out.top || out.left || out.bottom || out.right;
        //     out.all = out.top && out.left && out.bottom && out.right;

        //     return out;
        // }
    });
}(jQuery));