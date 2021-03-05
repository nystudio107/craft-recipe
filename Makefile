TAG?=12-alpine
CONTAINER?=recipe-buildchain
DEST?=../../sites/nystudio107/web/docs/recipe

.PHONY: docs install npm

docker:
	docker build \
		. \
		-t nystudio107/${CONTAINER}:${TAG} \
		--build-arg TAG=${TAG} \
		--no-cache
docs:
	docker container run \
		--name ${CONTAINER} \
		--rm \
		-t \
		-v `pwd`:/app \
		nystudio107/${CONTAINER}:${TAG} \
		run docs
	rm -rf ${DEST}
	mv ./docs/docs/.vuepress/dist ${DEST}
