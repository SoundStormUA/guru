/**
 * Created by TONY on 10.08.2015.
 */
(function(){
    //Tabs
    var firstTab = jQuery('#firstTab');
    var secondTab = jQuery('#secondTab');
    var thirdTab = jQuery('#thirdTab');

    //pages
    var fPage = jQuery('#first-tab-page');
    var sPage = jQuery('#second-tab-page');
    var tPage = jQuery('#third-tab-page');

    firstTab.click(function(e){
        tPage.hide();
        sPage.hide();
        fPage.show();
    });

    secondTab.click(function(e){
        tPage.hide();
        sPage.hide();
        fPage.show();
    });

    thirdTab.click(function(e){
        tPage.hide();
        sPage.hide();
        fPage.show();
    });
})();