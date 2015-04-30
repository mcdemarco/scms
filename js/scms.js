/* Adapted from strapdown.js for scms. */

var scms = {};

(function(context) {

	context.markdown = (function () {
		
		return {
			init: init,
			fetch: fetch,
			process: process
		};

		function fetch(url) {
			//The ajax loader is used for page loads from links.
			var xhr = new XMLHttpRequest();
			xhr.onload = load;
			xhr.open("GET", url, true);
			xhr.send();
		}

		function init() {
			//Initialize the menus and do the first process without a corresponding fetch.
			link();
			process();
		}

		function link() {
			//private
			var links = document.getElementsByTagName("nav")[0].getElementsByTagName("a");
			for (var l=0; l<links.length; l++) {
				var link = links[l];
				link.onclick = function(e) {
					scms.markdown.fetch(this.getAttribute("url"));
					return false;
				};
				link.href = "#";
			}
		}

		function load() {
			//private
			document.getElementsByTagName("xmp")[0].innerHTML = this.responseText;
			process();
		}
		
		function process() {
			//Generate HTML from Markdown.

			var markdownEl = document.getElementsByTagName('xmp')[0] || document.getElementsByTagName('textarea')[0];

			if (!markdownEl) {
				console.warn('No embedded Markdown found in this document.');
				return;
			}

	var markdown = markdownEl.textContent || markdownEl.innerText;
	
	var newNode = document.createElement('div');
	newNode.id = 'scms-markedUp';
	newNode.className = 'markdown-body';
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

	//Get the h2's.
	var panel = document.getElementById("scms-toc");
	var panelString = '';
	var theH2s = document.body.querySelectorAll("h2");
	for (h = 0; h < theH2s.length; h++) {
		theH2s[h].setAttribute("id","scms-tocAnchor" + h);
		panelString += "<li><a href='#scms-tocAnchor" + h + "'>" + theH2s[h].innerHTML + "</a></li>";
	}
	
	// Append.
	if (panelString) {
		panel.innerHTML = panelString;
	} else {
		document.getElementById("toc").style.display = 'none';
	}

			// Style.

		}
		
	})();
	
})(scms);

scms.markdown.init();
