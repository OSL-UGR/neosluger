const url_input    = document.getElementsByName("neosluger-url")[0];
const handle_input = document.getElementsByName("neosluger-handle")[0];


function hide_disabled_handle_msg ()
{
	handle_input.placeholder = "";
}


function toggle_handle ()
{
	let is_url = true;

	try
	{
		new URL(url_input.value);
	}
	catch
	{
		is_url = false;
	}

	handle_input.disabled = !is_url;
}


function show_disabled_handle_msg ()
{
	if (handle_input.disabled)
		handle_input.placeholder = "Introduce un enlace válido para poder elegir el nombre.";
}


url_input.addEventListener("input", toggle_handle);
handle_input.addEventListener("mouseenter", show_disabled_handle_msg)
handle_input.addEventListener("mouseleave", hide_disabled_handle_msg)
