// https://stackoverflow.com/a/39914235/10826835
function sleep (ms)
{
	return new Promise(resolve => setTimeout(resolve, ms));
}


async function copy_on_click (copy_div, popup_div)
{
	const copy_container = document.getElementById(copy_div);
	const text_to_copy   = copy_container.innerHTML.trim();
	const popup_text     = document.getElementById(popup_div);

	// Will throw error if connection is not HTTPS
	navigator.clipboard.writeText(text_to_copy);
	// Position the popup directly above the short URL
	popup_text.style.top = (copy_container.getBoundingClientRect().top - 34) + "px";

	popup_text.style.opacity = 1;
	await sleep(2000);
	popup_text.style.opacity = 0;
}
