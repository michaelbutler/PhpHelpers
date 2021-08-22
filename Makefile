
# Run unit tests, on bare metal without docker (simple)
test:
	XDEBUG_MODE=coverage vendor/bin/phpunit --bootstrap tests/bootstrap.php tests/ --coverage-text

# Build the docker container used for unit tests
build-test:
	docker build -t phphelpers:test .

# Run tests in docker (building first)
testd: build-test
	docker run phphelpers:test make test

# Run tests in docker with coverage HTML report
test-html: build-test
	mkdir -p build
	docker run --volume ${PWD}/build:/app/build phphelpers:test make test-html-inner

test-html-inner:
	XDEBUG_MODE=coverage vendor/bin/phpunit --bootstrap tests/bootstrap.php tests/ --coverage-html=./build/
	@echo "Coverage written to ./build/"
