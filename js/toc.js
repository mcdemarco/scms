// Table of Contents for scms.

(function(){
	// Move the navigation.
	var insertAfterMe = document.getElementById("headline");
	var insertMe = document.getElementById("scms-siteNavToolbar");
	insertAfterMe.parentNode.insertBefore(insertMe, insertAfterMe.nextSibling);
	/*
	// The old-style collapser goes before the headline.
	var dummy = document.createElement("div");
	dummy.innerHTML = '<a class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse"><span class="icon-bar"></span><span class="icon-bar"></span><span class="icon-bar"></span></a>';
	insertAfterMe.parentNode.insertBefore(dummy.firstChild, insertAfterMe);
	 */
	
	// Start the navigation panel.
	var panel = document.createElement("div");
	panel.setAttribute("id","scms-toc");
	panel.setAttribute("class","pull-right");
	panel.setAttribute("style","padding:0 10px;");
	var panelString = "";
	
	// Get the h2's.
	var theH2s = document.body.querySelectorAll("h2");
	for (h = 0; h < theH2s.length; h++) {
		theH2s[h].setAttribute("id","tocAnchor" + h);
		panelString += "<a href='#tocAnchor" + h + "' class='btn btn-default'>" + theH2s[h].innerHTML + "</a>";
	}
	
	// Append.
	if (panelString) {
		panel.innerHTML = '<div class="btn-toolbar" role="toolbar"><div class="btn-group-vertical">' + panelString + '</div></div>';
		var content = document.getElementById("content");
		content.insertBefore(panel,document.getElementsByTagName("h1")[0]);
	}
})();

