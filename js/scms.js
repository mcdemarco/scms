/* Adapted from strapdown.js for scms. */

(function(window, document) {

	//Generate HTML from Markdown.

	var markdownEl = document.getElementsByTagName('xmp')[0] || document.getElementsByTagName('textarea')[0];

	if (!markdownEl) {
		console.warn('No embedded Markdown found in this document.');
		return;
	}

	var markdown = markdownEl.textContent || markdownEl.innerText;
	
	var newNode = document.createElement('div');
	newNode.id = 'scms-markedUp';
	document.getElementById("content").appendChild(newNode);
	
	// Generate Markdown
	var html = marked(markdown);
	document.getElementById('scms-markedUp').innerHTML = html;
	
	// Style tables
	var tableEls = document.getElementsByTagName('table');
	for (var i=0, ii=tableEls.length; i<ii; i++) {
		var tableEl = tableEls[i];
		tableEl.className = 'table table-striped table-bordered';
	}

	//Generate TOC.

	// Get the h2's.
	var panel = document.getElementById("scms-toc");
	var panelString = '';
	var theH2s = document.body.querySelectorAll("h2");
	for (h = 0; h < theH2s.length; h++) {
		theH2s[h].setAttribute("id","scms-tocAnchor" + h);
		panelString += "<a class='list-group-item' href='#scms-tocAnchor" + h + "'>" + theH2s[h].innerHTML + "</a></div>";
	}
	
	// Append.
	if (panelString) {
		panel.innerHTML = panelString;
	}
	
})(window, document);
