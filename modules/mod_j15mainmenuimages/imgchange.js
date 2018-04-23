window.addEvent('domready', function() {
$$('#current img, .active img').each(function(img) {
	var src = img.getProperty('src');
	var extension = src.substring(src.lastIndexOf('.'),src.length)
        img.setProperty('src',src.replace(extension,'-active' + extension)); 
});
$$('.menu img').each(function(img) {
	var src = img.getProperty('src'); 
	var extension = src.substring(src.lastIndexOf('.'),src.length)
	img.addEvent('mouseenter', function() { img.setProperty('src',src.replace(extension,'-hover' + extension)); });
	img.addEvent('mouseleave', function() { img.setProperty('src',src);
});
}); 
});