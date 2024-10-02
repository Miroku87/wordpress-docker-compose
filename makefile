.PHONY: dev
dev:
	docker-compose up
	
.PHONY: run
run:
	docker-compose up -d
	
.PHONY: stop
stop:
	docker-compose down
	
.PHONY: restart
restart: stop run

.PHONY: release-redeemable-codes
release-redeemable-codes:
	./ftp-upload.sh \
		ftp.viaggionelmultiverso.it \
		ipfzhzcr \
		${MULTIVERSO_FTP_PASSWORD} \
		/public_html/wp-content/plugins/redeemable-codes \
		plugins/redeemable-codes

.PHONY: release-multiverso-leaderboard
release-multiverso-leaderboard:
	./ftp-upload.sh \
		ftp.viaggionelmultiverso.it \
		ipfzhzcr \
		${MULTIVERSO_FTP_PASSWORD} \
		/public_html/wp-content/plugins/multiverso-leaderboard \
		plugins/multiverso-leaderboard