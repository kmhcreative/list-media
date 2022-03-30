//Medialist Features
//Medialist Version: 1.3.9
var $listmediajq = jQuery.noConflict();
$listmediajq (document).ready(function($){
	var mediaconstruct = $listmediajq("div[mediajqref='listmedia-construct']");//target listmedia elements only and cache it.
	$listmediajq(mediaconstruct).each(function(index){
		//page data tag defines
        var listmediainstance = $listmediajq(this).data('instance');
		var listmedialmtoken = $listmediajq(this).data('token');
		var listmediapaginate = $listmediajq(this).data('paging');
        setlmpage(listmediainstance,listmedialmtoken,listmediapaginate);
	});
	function setlmpage(listmediainstance,listmedialmtoken,listmediapaginate){
	    var listmediacache = $listmediajq ("#lmid-" + listmediainstance)
		$listmediajq(listmediacache).each(function () {//do the below for each listmedia instance on the page.
			var foo = $listmediajq (this);
			//adjust offset
			var passmaxitems = listmediapaginate-1;
			var items = passmaxitems+1; //items to display adjusted to account for offset, make items to display true to setting.
			var pages = 1; //used for checking against paginate setting
			//calculate pages
			var totalitems = $listmediajq("#lmid-"+listmediainstance+" ul li").size(); //how many items available in total per instance.
			var addpage = 0; //'items' value are added to 'addpage' via loop.
			var maxpages = 0; //incremented once each loop cycle to give us the total pages minus any remainder
			var nextpage = 1;//used for the current page of items being displayed
			var remainder = (totalitems)%(items);//calculate any remaining items.
//Build pagination
			if ((listmediapaginate >= pages) && (totalitems > passmaxitems) && (totalitems !=1)){
				var vpages = passtojq.vpages;
				var voffsep = passtojq.voffsep;
				var vprev = passtojq.vprev;
				var vnext = passtojq.vnext;
				var pageappend = $listmediajq (this).append('<div class="lmpagination"><div style="float:left; padding-top:12px;" class="listmedia-page-meta"><p class="listmedia-page-data">'+vpages+'</p> <div class="listmedia-page-data lmpageoff-'+listmediainstance+'">1</div> <p class="listmedia-page-data">'+voffsep+'</p> <div class="listmedia-page-data lmmaxpage-'+listmediainstance+'"></div></div><div style="width:390px;" class="listmedia-buttons"><a class="prev">'+vprev+'</a><a class="next">'+vnext+'</a></div></div>');
				var itemhide = $listmediajq (this).find(".lm-ul li:gt(" +passmaxitems+ ")").hide();
				var lmhide = $listmediajq("#lmid-"+listmediainstance+" ul li").filter(":hidden").size(); //count hidden items
				$listmediajq (itemhide,pageappend);//hide items beyond set items to display and append pagination divs.
				while (addpage <= lmhide){//checking total hidden items is less than or equal to addpage
					addpage += items;//'items' added to 'addpage'. 
					maxpages++;//increments our page total each loop cycle until condition met.
				}
				if (remainder > 0){//if any remaining items found increment our page total to give the true page total.
					maxpages++;
				}
				$listmediajq('div.lmmaxpage-'+listmediainstance).text(maxpages);//write our max pages to unique class in html.
//Page buttons
				var medianext = $listmediajq (this).find('.next')
				var mediaprev = $listmediajq (this).find('.prev')
				mediaprev.hide(); // we know first page has no previous
				//set next button
				var mediapageoff = $listmediajq('div.lmpageoff-'+listmediainstance)
				$listmediajq (medianext).click(function () {
					console.log('click next');
					if (checknext()) {
					var last = $listmediajq ('.lm-ul',foo).children('li:visible:last');
					last.nextAll(":lt(" +items+ ")").show();
					last.next().prevAll().hide();
					nextpage++;//increment page on-click
					$listmediajq(mediapageoff).text(nextpage);
					checknext();
					checkprev();
					}
				});
				//set previous button
				$listmediajq (mediaprev).click(function () {
					console.log('click prev');
					if (checkprev()) {
					var first = $listmediajq ('.lm-ul',foo).children('li:visible:first');
						first.prevAll(":lt(" +items+ ")").show();
						first.prev().nextAll().hide();
						nextpage--;	//decrement page on-click
						$listmediajq(mediapageoff).text(nextpage);
						checkprev();
						checknext();
					}
				});
			} else if (listmediapaginate < pages){
				$listmediajq (this).find(".lm-ul li:gt(" +passmaxitems+ ")").hide();
			}
			function checknext(){
				if (nextpage == maxpages){
					console.log('hide next');
					medianext.hide();
					return false;
				}
				console.log('show next');
				medianext.show();
				return true;
			}//checks if maximum page is equal to current page, if it is prevent click.
			function checkprev(){
				if (nextpage == 1){
					console.log('hide prev');
					mediaprev.hide();
					return false;
				}
				console.log('show prev');
				mediaprev.show();
				return true;
			}//checks if current page is first page, if it is prevent click.
//Search Functions
			$listmediajq.expr[":"].contains = $listmediajq.expr.createPseudo(function(arg) { //Allow contains to be case-insensitive.
				return function( elem ) {return $listmediajq(elem).text().toUpperCase().indexOf(arg.toUpperCase()) >= 0;};
			});
			//cache re-used elements.
			var findhide = $listmediajq("#lmid-"+listmediainstance+" ul li");
			var uniqueinstance = $listmediajq ("#lmid-" +listmediainstance);
			$listmediajq (this).find('.listmedia-gosearch').click(function () {
				//On click hide all li items in listmediainstance, search for match using modified 'contains' and display.
			    var listmediasearch = $listmediajq('.lm-search-'+listmediainstance).val();
				if($listmediajq('.lm-search-'+listmediainstance).val() == ''){return false;}
				$listmediajq(findhide.addClass('listmedia-hidden'));
				$listmediajq (uniqueinstance).find('.lmpagination').addClass('listmedia-hidden');
				$listmediajq("#lmid-"+listmediainstance+" ul li:contains('"+listmediasearch+"')").addClass('listmedia-active');
			});
			var listmediakeyup = $listmediajq('.lm-search-'+listmediainstance)
			$listmediajq(listmediakeyup).keyup(function(){//Restores the listmedia when field emptied.
				if($listmediajq('.lm-search-'+listmediainstance).val() == ''){
					$listmediajq (uniqueinstance).find('.lmpagination').removeClass('listmedia-hidden');
					$listmediajq(findhide).removeClass('listmedia-hidden listmedia-active');
				}
			});
		//end of Search Functions
        });//end of #lmid each function
	}//close setlmpage function 
});//end document.ready


































    
	
	
	
	

