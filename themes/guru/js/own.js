/**
 * Created by TONY on 10.08.2015.
 */
(function(){
    //Tabs
    var fistTab = $('#firstTab');
    var secondTab = $('#secondTab');
    var thirdTab = $('#thirdTab');

    //pages
    var fPage = $('#first-tab-page');
    var sPage = $('#second-tab-page');
    var tPage = $('#third-tab-page');

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