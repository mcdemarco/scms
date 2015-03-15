/* Adapted from strapdown.js for scms. */

(function(window, document) {

	var markdownEl = document.getElementsByTagName('xmp')[0] || document.getElementsByTagName('textarea')[0];

	if (!markdownEl) {
		console.warn('No embedded Markdown found in this document for Strapdown.js to work on! Visit http://strapdownjs.com/ to learn more.');
		return;
	}

	// Hide body.
	document.body.style.display = 'none';
	
	var markdown = markdownEl.textContent || markdownEl.innerText;
	
	var newNode = document.createElement('div');
	newNode.className = 'container';
	newNode.id = 'content';
	document.body.replaceChild(newNode, markdownEl);
	
	// Generate Markdown
	var html = marked(markdown);
	document.getElementById('content').innerHTML = html;
	
	// Style tables
	var tableEls = document.getElementsByTagName('table');
	for (var i=0, ii=tableEls.length; i<ii; i++) {
		var tableEl = tableEls[i];
		tableEl.className = 'table table-striped table-bordered';
	}

	// Show body
	document.body.style.display = '';

	//Generate TOC.

	// Get the h2's.
	var panel = document.getElementById("scms-toc");
	var panelString = '';
	var theH2s = document.body.querySelectorAll("h2");
	for (h = 0; h < theH2s.length; h++) {
		theH2s[h].setAttribute("id","scms-tocAnchor" + h);
		panelString += "<div class='panel-body'><a href='#scms-tocAnchor" + h + "'>" + theH2s[h].innerHTML + "</a></div>";
	}
	
	// Append.
	if (panelString) {
		panel.innerHTML = '<div class="panel-heading">Table of Contents</div>' + panelString;
	}
	
})(window, document);
