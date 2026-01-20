(function () {
	const config = window.Side8SocialHeart;
	if (!config) {
		return;
	}

	window.wp.apiFetch.use(window.wp.apiFetch.createNonceMiddleware(config.nonce));

	const form = document.querySelector('[data-side8-form]');
	const notice = document.querySelector('[data-side8-notice]');
	const activityList = document.querySelector('[data-side8-activity]');
	const postSelect = form ? form.querySelector('select[name="post_id"]') : null;

	const getErrorStatus = (error) => {
		if (!error) {
			return null;
		}
		if (error.data && typeof error.data.status !== 'undefined') {
			return error.data.status;
		}
		return error.status || null;
	};

	const getUserMessage = (error, fallback) => {
		const status = getErrorStatus(error);
		if (status === 401 || status === 403 || error.code === 'rest_cookie_invalid_nonce' || error.code === 'side8_nonce_invalid') {
			return 'Your session expired. Please refresh the page and try again.';
		}
		if (status === 502 || status === 503) {
			return 'Posting service is temporarily unavailable. Your submission was saved. Please try again soon.';
		}
		return fallback;
	};

	const showNotice = (message, isError) => {
		if (!notice) {
			return;
		}
		notice.textContent = message;
		notice.style.color = isError ? '#b91c1c' : '#065f46';
	};

	const fetchActivity = async () => {
		if (!activityList) {
			return;
		}
		activityList.innerHTML = '<li>Loading activity...</li>';
		try {
			const response = await window.wp.apiFetch({
				path: '/side8/v1/activity',
			});
			activityList.innerHTML = '';
			if (!response || response.length === 0) {
				activityList.innerHTML = '<li>No activity yet.</li>';
				return;
			}
			response.forEach((item) => {
				const li = document.createElement('li');
				li.textContent = `${item.action}: ${item.message}`;
				activityList.appendChild(li);
			});
		} catch (error) {
			const message = getUserMessage(error, 'Unable to load activity.');
			console.error('Side8 Social Heart error:', error);
			activityList.innerHTML = `<li>${message}</li>`;
		}
	};

	if (form) {
		form.addEventListener('submit', async (event) => {
			event.preventDefault();
			const formData = new FormData(form);
			const payload = {
				post_id: formData.get('post_id'),
				caption: formData.get('caption'),
				channels: formData.getAll('channels[]'),
				title: postSelect && postSelect.selectedOptions.length
					? postSelect.selectedOptions[0].textContent
					: 'Submission',
			};

			showNotice('Submitting...', false);
			try {
				await window.wp.apiFetch({
					path: '/side8/v1/submit',
					method: 'POST',
					data: payload,
				});
				showNotice('Submitted for approval.', false);
				form.reset();
				fetchActivity();
			} catch (error) {
				const message = getUserMessage(error, 'Unable to submit. Please try again.');
				console.error('Side8 Social Heart error:', error);
				showNotice(message, true);
			}
		});
	}

	fetchActivity();
})();
