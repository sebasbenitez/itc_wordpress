const htmlElement = document.getElementsByTagName('html')[0];
const windowWidth = window.innerWidth;
const htmlOffsetWidth = htmlElement.offsetWidth;
const userAgent = navigator.userAgent;

let shouldCalculateScrollbar = windowWidth > 1024 && windowWidth > htmlOffsetWidth;

if (userAgent.includes('Chrome')) {
	const match = userAgent.match(/Chrome\/(\d+)/);
	if (match) {
		const version = parseInt(match[1], 10);

		if (version >= 145) {
			shouldCalculateScrollbar = false;
		}
	}
}

if (shouldCalculateScrollbar) {
	const scrollbarWidth = windowWidth - htmlOffsetWidth;
	const styleElement = document.createElement('style');

	styleElement.textContent = `:root {--wd-scroll-w: ${scrollbarWidth}px;}`;
	document.head.appendChild(styleElement);
}