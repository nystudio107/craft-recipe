/**
 * Recipe plugin for Craft CMS
 *
 * Recipe Field JS
 *
 * @author    nystudio107
 * @copyright Copyright (c) 2017 nystudio107
 * @link      https://nystudio107.com
 * @package   Recipe
 * @since     1.0.0
 */

 ;(function ( $, window, document, undefined ) {

    var pluginName = "RecipeRecipe",
        defaults = {
        };

    // Plugin constructor
    function Plugin( element, options ) {
        this.element = element;

        this.options = $.extend( {}, defaults, options) ;

        this._defaults = defaults;
        this._name = pluginName;

        this.init();
    }

    Plugin.prototype = {

        init: function(id) {
            var _this = this;

            $(function () {

/* -- _this.options gives us access to the $jsonVars that our FieldType passed down to us */

                // Tab handler
                $('.recipe-tab-links').on('click', function(e) {
                    e.preventDefault();
                    $('.recipe-tab-links').removeClass('sel');
                    $(this).addClass('sel');
                    $('.recipe-tab-content').addClass('hidden');
                    var selector = $(this).attr('href');
                    $(selector).removeClass('hidden');
                    // Trigger a resize to make event handlers in Garnish activate
                    Garnish.$win.trigger('resize');
                    // Fixes Redactor fixed toolbars on previously hidden panes
                    Garnish.$doc.trigger('scroll');
                });

                // Fetch nutritional info handler
                $('.fetch-nutritional-info a').on('click', function(e) {
                    e.preventDefault();
                    if ($(this).hasClass('disabled')) {
                        return;
                    }
                    var ingredients = [];
                    $('#fields-recipeingredients tbody tr').each(function() {
                        var ingredient = [];
                        $(this).find('textarea, select').each(function() {
                            ingredient.push($(this).val());
                        })
                        ingredients.push(ingredient.join(' '));
                    });
                    var recipe = {
                        name: $('#fields-recipename').val(),
                        serves: $('#fields-recipeserves').val(),
                        ingredients: ingredients,
                    };
                    $('.fetch-nutritional-info a').addClass('disabled');
                    $('.fetch-nutritional-info .spinner').removeClass('hidden');
                    $.getJSON($(this).attr('data-url'), recipe, function(data) {
                        $.each(data, function(index, value) {
                            $('#fields-recipe' + index).val(value);
                        });
                        $('.fetch-nutritional-info a').removeClass('disabled');
                        $('.fetch-nutritional-info .spinner').addClass('hidden');
                    });
                });

            });
        }
    };

    // A really lightweight plugin wrapper around the constructor,
    // preventing against multiple instantiations
    $.fn[pluginName] = function ( options ) {
        return this.each(function () {
            if (!$.data(this, "plugin_" + pluginName)) {
                $.data(this, "plugin_" + pluginName,
                new Plugin( this, options ));
            }
        });
    };

})( jQuery, window, document );
