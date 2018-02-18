.PHONY: test

test:
	vendor/bin/tester tests -c tests/php.ini-unix -p php

