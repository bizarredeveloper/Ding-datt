// JavaScript Document

$(document).ready(function() {
	
$(function() {
		$('.sidebar_left').stop().animate({'width':'42'},1000);

		$('.sidebar_left').hover(
			function () {
				$($(this)).stop().animate({'width':'200px'},200);
				$('body').addClass('nav-fixed-open');
				$('body').removeClass('nav-fixed-close');
			},
			function () {
				$($(this)).stop().animate({'width':'42'},200);
				$('body').addClass('nav-fixed-close');
				$('body').removeClass('nav-fixed-open');
				$(".menu-main li").removeClass("hover");
			}
		);
	});
$('.navEx-on').click(function()
{
	$('body').toggleClass('nav-fixed-lock-open');
	$('.sidebar_left').toggleClass('ibi_expan');
	$(this).toggleClass('act');
	
	   $('.navEx-on .lock-nav').animate({
		//'left':'0'
		},0.0001);
		
		$('.navEx-on.act .lock-nav').animate({
		//'left':'50px'
		},0.0001);
	 }
	 
);
});

/****************************  logged user toggle *****************************/
$(document).ready(function(e) 
{ 
    $('.logged-user-panel').click(function()
	{
		$('.user-tog-box').slideToggle();
	});
});
var adjusheight = function() {
	var winHeight = $(window).height();
	var rightColumn = $('.scroll-wrapper');
	$(rightColumn).css('height', (winHeight - 74));
}

$(document).ready(function(e) 
{ 
   adjusheight();
});
$(window).bind('resize orientationchange', function() {
	adjusheight();
});
/****************************  drop down scroll start *****************************/
$(function()
{
	$('.navigation-scroll').jScrollPane();
});
/****************************  tab section start *****************************/
$(document).ready(function(){
	$(".tabContents").hide(); // Hide all tab content divs by default
	$(".tabContents:first").show(); // Show the first div of tab content by default
	
	$(".tabContaier ul li a").click(function(){ //Fire the click event
		
		var activeTab = $(this).attr("href"); // Catch the click link
		$(".tabContaier ul li a").removeClass("active"); // Remove pre-highlighted link
		$(this).addClass("active"); // set clicked link to highlight state
		$(".tabContents").hide(); // hide currently visible tab content div
		$(activeTab).fadeIn(); // show the target tab content div by matching clicked link.
		
		return false; //prevent page scrolling on tab click
	});
});
/****************************  table odd even row *****************************/
$(document).ready(function() {
	
});
$(document).ready(function() {
	
	
});
/****************************  table odd even row *****************************/
$(document).ready(function(){
	$("#student-listing-table tbody tr:odd").addClass('even'); 
	$("#student-listing-table tbody tr:even").addClass('odd'); 
});

$(document).ready(function(){

$('.datetimepicker1').datetimepicker({
	//yearOffset:222,
	//lang:'ch',
	timepicker:false,
	format:'Y/m/d',
	formatDate:'Y/m/d',
	//minDate:'-1970/01/02', // yesterday is minimum date
	//maxDate:'+1970/01/02' // and tommorow is maximum date calendar
});

$('.datetimepicker2').datetimepicker({
	datepicker:false,
	format:'H:i',
	step:6
});

$('.datetimepicker3').datetimepicker({
	//yearOffset:222,
	//lang:'ch',
	timepicker:true,
	format:'d/m/Y',
	formatDate:'Y/m/d',
	//minDate:'-1970/01/02', // yesterday is minimum date
	//maxDate:'+1970/01/02' // and tommorow is maximum date calendar
});


});

