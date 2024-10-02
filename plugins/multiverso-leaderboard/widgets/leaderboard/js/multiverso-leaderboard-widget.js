import VanillaDataTable from './vanilla-datatable.js';

(function () {
    'use strict';

    const init = () => {
        document.querySelectorAll('table.leaderboard').forEach(e => {
            const leaderboard = new VanillaDataTable(e);
            leaderboard.init();
        });
    };

    // wait for the DOM to have loaded
    document.addEventListener('DOMContentLoaded', init);
})();
