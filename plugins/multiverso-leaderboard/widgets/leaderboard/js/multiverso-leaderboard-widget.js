import VanillaDataTable from './vanilla-datatable.js';

(function () {
    'use strict';

    const init = () => {
        const leaderboard = new VanillaDataTable(document.querySelector('#leaderboard'));
        leaderboard.init();
    };

    // wait for the DOM to have loaded
    document.addEventListener('DOMContentLoaded', init);
})();
