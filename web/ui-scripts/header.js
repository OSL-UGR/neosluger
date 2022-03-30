const close_button  = document.getElementById("close-vertical-menu");
const hamburger     = document.getElementById("hamburger-menu");
const hamburger_img = hamburger.getElementsByTagName("img")[0];
const menu_items    = document.getElementById("menu-items");
const item_links    = menu_items.getElementsByTagName("a");


function close_hamburger_menu ()
{
	close_button.style.display = "none";
	hamburger.onclick          = open_hamburger_menu;
	hamburger_img.src          = "/assets/material/menu.svg";
	hamburger_img.style.rotate = "0deg";
	menu_items.style.width     = "0vw";

	for (let link of item_links)
	{
		link.style.left  = "-150px";
		link.style.width = "0";
	}
}


function open_hamburger_menu ()
{
	close_button.style.display = "block";
	hamburger.onclick          = close_hamburger_menu;
	hamburger_img.style.rotate = "180deg";
	hamburger_img.src          = "/assets/material/close.svg";
	menu_items.style.width     = "50vw";

	for (let link of item_links)
	{
		link.style.left  = "0px";
		link.style.width = "calc(100% - 60px)";
	}
}
