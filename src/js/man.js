function switch_api ()
{
	const new_api = document.querySelector("#new-api");
	const old_api = document.querySelector("#old-api");

	const new_api_style = window.getComputedStyle(new_api);
	const old_api_style = window.getComputedStyle(old_api);

	const switch_api = document.querySelector("#switch-api");
	const inner_html = switch_api.innerHTML;

	if (new_api_style.display === "none")
	{
		new_api.style.display = "contents";
		switch_api.innerHTML = inner_html.replace("nueva", "antigua");
	}
	else
	{
		new_api.style.display = "none";
	}

	if (old_api_style.display === "none")
	{
		old_api.style.display = "contents";
		switch_api.innerHTML = inner_html.replace("antigua", "nueva");
	}
	else
	{
		old_api.style.display = "none";
	}
}

function switch_tab (event, tabname)
{
	// Toggles the visible manual content
	document.querySelector(".visible").className  = "invisible";
	document.querySelector("#"+tabname).className = "visible";

	// Toggles the current tabline button
	document.querySelector("#current").removeAttribute("id");
	event.currentTarget.id = "current";
}
