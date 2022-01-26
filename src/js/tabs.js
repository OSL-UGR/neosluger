function switch_tab (event, tabname)
{
	// Toggles the visible manual content
	document.querySelector(".visible").className  = "invisible";
	document.querySelector("#"+tabname).className = "visible";

	// Toggles the current tabline button
	document.querySelector("#current-tab-item").removeAttribute("id");
	event.currentTarget.id = "current-tab-item";
}
