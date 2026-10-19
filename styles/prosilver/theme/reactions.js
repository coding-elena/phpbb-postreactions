(function () {
	'use strict';

	if (!window.fetch || !window.URLSearchParams) {
		return;
	}

	document.addEventListener('click', function (event) {
		var link = event.target.closest('a[data-reaction-key]');
		if (!link) {
			return;
		}

		var container = link.closest('.post-reaction-container');
		if (!container || container.classList.contains('is-loading')) {
			return;
		}

		event.preventDefault();
		container.classList.add('is-loading');
		container.setAttribute('aria-busy', 'true');

		var body = new URLSearchParams();
		body.set('reaction', link.dataset.reactionKey);
		body.set('hash', container.dataset.hash);

		window.fetch(container.dataset.ajaxUrl, {
			method: 'POST',
			credentials: 'same-origin',
			headers: {
				'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8'
			},
			body: body.toString()
		}).then(function (response) {
			return response.json().then(function (data) {
				if (!response.ok) {
					throw new Error(data.error || container.dataset.errorMessage);
				}

				return data;
			});
		}).then(function (data) {
			updateReactionList(container, data.reactions);
			container.querySelector('.reaction-status').textContent = '';
		}).catch(function (error) {
			container.querySelector('.reaction-status').textContent = error.message || container.dataset.errorMessage;
		}).then(function () {
			container.classList.remove('is-loading');
			container.removeAttribute('aria-busy');
		});
	});

	function updateReactionList(container, reactions) {
		var wrapper = container.querySelector('.post-reactions');
		var picker = wrapper.querySelector('.reaction-picker');
		var options = Array.prototype.slice.call(wrapper.querySelectorAll('.reaction-picker-option'));
		var existing = wrapper.querySelectorAll('.post-reaction');

		Array.prototype.forEach.call(existing, function (reaction) {
			reaction.remove();
		});

		reactions.forEach(function (reaction) {
			var option = options.find(function (item) {
				return item.dataset.reactionKey === reaction.key;
			});
			var button = document.createElement('a');
			button.className = 'post-reaction' + (reaction.selected ? ' is-selected' : '');
			button.href = option ? option.href : '#';
			button.dataset.reactionKey = reaction.key;
			button.title = reaction.emoji;
			button.setAttribute('aria-label', reaction.emoji);

			var emoji = document.createElement('span');
			emoji.className = 'post-reaction-emoji';
			emoji.textContent = reaction.emoji;
			button.appendChild(emoji);

			if (reaction.count > 1) {
				var count = document.createElement('span');
				count.className = 'post-reaction-count';
				count.textContent = reaction.count;
				button.appendChild(count);
			}

			wrapper.insertBefore(button, picker || wrapper.firstChild);
		});

		options.forEach(function (option) {
			option.classList.toggle(
				'is-selected',
				reactions.some(function (reaction) {
					return reaction.key === option.dataset.reactionKey && reaction.selected;
				})
			);
		});

		if (picker) {
			picker.open = false;
		}
	}
}());
