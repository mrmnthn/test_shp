PWD := $(realpath $(dir $(lastword $(MAKEFILE_LIST))))
DOCKER_PS := $$(docker-compose ps -q)

DOCKER_COMPOSE_PROJECT_DIR = $(notdir $(CURDIR))
DOCKER_COMPOSE_PROJECT_NAME := $(subst -,,${DOCKER_COMPOSE_PROJECT_DIR})

init:
	@echo "Creating Docker containers..." && \
	@docker-compose up --build -d

stop:
	@echo "Stopping containers..." && \
	@docker-compose stop