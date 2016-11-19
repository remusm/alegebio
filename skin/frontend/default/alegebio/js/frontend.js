	Cufon.replace('h2');
	Cufon.replace('h1');
	Cufon.replace('h3', { hover:true });
	Cufon.replace('#search p');
	Cufon.replace('#main li a', { textShadow: '#5d6d2f 1px 1px', hover :{ textShadow: '#5d6d2f 2px 2px'} } );

	/*Cufon.replace('#navigation a', {hover:true});
	Cufon.replace('#product-menu>ul>.cat-item>a', {hover:true});*/
	Cufon.now();


/* externalLinks opens rel="external" links in a new window/tab */
function externalLinks() { if (!document.getElementsByTagName) return; var anchors = document.getElementsByTagName("a"); for (var i=0; i<anchors.length; i++) { var anchor = anchors[i]; if (anchor.getAttribute("href") && anchor.getAttribute("rel") == "external") anchor.target = "_blank"; } } window.onload = externalLinks;