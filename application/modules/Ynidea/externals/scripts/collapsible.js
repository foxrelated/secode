
window.addEvent('domready', function() {
   $$('.ynidea-category-sub-category').set('styles', {
        display : 'none'
    });
    
     $$('.ynidea-category-collapse-control').addEvent('click', function(event) {

        var row = this.getParent('li');

        if (this.hasClass('ynidea-category-collapsed')) {

        	var id = row.getAttribute('value');
        	var rowSubCategories = row.getAllNext('li.child_'+id);  

            this.removeClass('ynidea-category-collapsed');
            this.addClass('ynidea-category-no-collapsed');

            for(var i = 0; i < rowSubCategories.length; i++) {

                if (!rowSubCategories[i].hasClass('ynidea-category-sub-category')) {
                    break;
                } else {
                    rowSubCategories[i].set('styles', {
                        display : 'block'
                    });
                }
            }

        } else {

        	var rowSubCategories = row.getAllNext('li');

            this.removeClass('ynidea-category-no-collapsed');
            this.addClass('ynidea-category-collapsed');

            for(var i = 0; i < rowSubCategories.length; i++) {

                if (!rowSubCategories[i].hasClass('ynidea-category-sub-category')) {
                    break;
                } else {
                	var collapsedDivs = rowSubCategories[i].getElements('.ynidea-category-collapse-control');

                	if (collapsedDivs.length > 0) {
                		collapsedDivs[0].removeClass('ynidea-category-no-collapsed');
                		collapsedDivs[0].addClass('ynidea-category-collapsed');
                	}

                    rowSubCategories[i].set('styles', {
                        display : 'none'
                    });
                }
            }
        }
    }); 
});