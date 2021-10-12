#!/usr/bin/env bash

export USE_HOSTNAME=fakturoid.tomaskulhanek.cz
echo $USE_HOSTNAME > /etc/hostname
hostname -F /etc/hostname

docker swarm init
docker swarm init --advertise-addr __VEREJNA_IP__


docker network create --driver=overlay traefik-public
export NODE_ID=$(docker info -f '{{.Swarm.NodeID}}')
docker node update --label-add traefik-public.traefik-public-certificates=true $NODE_ID
export EMAIL=shoptet@tomaskulhanek.cz
export DOMAIN=traefik.sys.fakturoid.tomaskulhanek.cz
export USERNAME=tomas.kulhanek
export HASHED_PASSWORD=$(openssl passwd -apr1)
echo $HASHED_PASSWORD
docker stack deploy -c traefik.yml traefik


export DOMAIN=portainer.sys.fakturoid.tomaskulhanek.cz
export NODE_ID=$(docker info -f '{{.Swarm.NodeID}}')
docker node update --label-add portainer.portainer-data=true $NODE_ID
docker stack deploy -c portainer.yml portainer

