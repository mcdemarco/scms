/* Adapted from strapdown.js for scms. */

var scms = {};

(function(context) {

	context.markdown = (function () {
		
		return {
			init: init,
			fetch: fetch
		};

		function fetch(url) {
			//The ajax loader is used for page loads from links.
			var xhr = new XMLHttpRequest();
			xhr.onload = load;
			xhr.open("GET", url, true);
			xhr.send();
			history.pushState(null, null, url.substring(0, url.lastIndexOf(".")) + location.search);
		}

		function generateTOC() {
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
				document.getElementById("toc").style.display = 'block';
			} else {
				document.getElementById("toc").style.display = 'none';
			}

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
				if (link.getAttribute("markdown") == 'true') {
					link.onclick = function(e) {
						scms.markdown.fetch(this.getAttribute("url"));
						return false;
					};
				link.href = "#";
				} else {
					link.href += location.search;
				}
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
			} else {
				var markdown = markdownEl.textContent || markdownEl.innerText;
			}

			//We (re)insert the target element to avoid CSS issues.
			if (document.getElementById("scms-markedUp")) {
				document.getElementById("content").removeChild(document.getElementById("scms-markedUp"));
			}
			
			var newNode = document.createElement('div');
			newNode.id = 'scms-markedUp';
			newNode.className = 'markdown-body';
			document.getElementById("content").appendChild(newNode);

			// Generate Markdown and write to target
			var html = marked(markdown);
			document.getElementById('scms-markedUp').innerHTML = html;

			relink();
			style();
			generateTOC();
		}

		function relink() {
			//Find any local links in the processed markdown and hijack them for ajax.

			//...but how?
		}
		
		function style() {
			//Style tables
			var tableEls = document.getElementsByTagName('table');
			for (var i=0, ii=tableEls.length; i<ii; i++) {
				var tableEl = tableEls[i];
				tableEl.className = 'table table-striped table-bordered';
			}
		}
		
	})();
	
})(scms);

scms.markdown.init();
