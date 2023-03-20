.PHONY: composer
composer composer-install:
	docker run --rm --interactive --tty \
		--volume $(PWD):/app \
		composer $(CMD)

composer-install: CMD=install

.PHONY: test
test:
	vendor/bin/phpunit
