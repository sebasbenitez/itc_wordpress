jQuery('body').on('click','#attrsCheckAll, #attrsUncheckAll',function(e)
{
    e.preventDefault();

    var dontDisplayTypes = ['[product_type]']; // An array of types not allowed to display All|None check buttons
    if (!jQuery(this).parent().next().find('input').prop('name')) {
        return;
    }
    var shouldDisplayAllNoneButtons = true;
    var it = jQuery(this);
    dontDisplayTypes.forEach(function (elem, index) {
        if (it.parent().next().find('input').prop('name').toString().indexOf('[product_type]') >= 0) {
            shouldDisplayAllNoneButtons = false;
        }
    });

    if (shouldDisplayAllNoneButtons) {
        if (e.target.id == 'attrsCheckAll') {
            jQuery(this).parent().next().find('input').prop('checked', true);
        } else if (e.target.id == 'attrsUncheckAll') {
            jQuery(this).parent().next().find('input').prop('checked', false);
        }
    }
});
