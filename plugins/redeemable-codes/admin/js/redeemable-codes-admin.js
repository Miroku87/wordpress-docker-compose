(function () {
	'use strict';

	const onCodeTypeChange = (codeTypeInput) => {
		return (e) => {
			const codeType = e.target.value;
			codeTypeInput.classList.remove('code-type-custom', 'code-type-random');
			codeTypeInput.classList.add(`code-type-${codeType}`);
		};
	}

	const setupCodesForm = () => {
		const codesForm = document.getElementById('codes_form');
		if (!codesForm) {
			return;
		}

		const codeTypeMenu = document.getElementById('code_type');
		const codeTypeInput = document.getElementById('form_table');

		codeTypeMenu.addEventListener('change', onCodeTypeChange(codeTypeInput));
	}

	const init = () => {
		setupCodesForm();
	};

	// wait for the DOM to have loaded
	document.addEventListener('DOMContentLoaded', init);
})();
