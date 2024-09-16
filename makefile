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
	cp -r ./plugins/redeemable-codes ../speedtale-wp/wp-content/plugins/

.PHONY: release-multiverso-leaderboard
release-multiverso-leaderboard:
	./ftp-upload.sh \
		ftp.viaggionelmultiverso.it \
		ipfzhzcr \
		${MULTIVERSO_FTP_PASSWORD} \
		/public_html/wp-content/plugins/multiverso-leaderboard \
		plugins/multiverso-leaderboard

# .PHONY: release-multiverso-leaderboard
# release-multiverso-leaderboard:
# 	curl -T 'plugins/multiverso-leaderboard' 'ftp://ipfzhzcr:${MULTIVERSO_FTP_PASSWORD}@ftp.viaggionelmultiverso.it/public_html/wp-content/plugins/'