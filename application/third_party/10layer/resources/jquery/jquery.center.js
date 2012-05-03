jQuery.fn.center = function () {
	//Get the window height and width
	var winH = $(window).height();
	var winW = $(window).width();
	
	var top_pos = winH/2-$(this).height()/2;
	var left_pos = winW/2-$(this).width()/2;
              
		//Set the popup window to center
	this.css('top',  top_pos);
	this.css('left', left_pos);

    this.css("position","absolute");
    return this;
}