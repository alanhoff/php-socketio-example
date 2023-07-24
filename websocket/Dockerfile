FROM node:18-alpine

# Create app directory
WORKDIR /usr/src/app

# Install init system
RUN apk add --update --no-cache tini
ENTRYPOINT ["tini", "--"]

# Install app dependencies
# A wildcard is used to ensure both package.json AND package-lock.json are copied
# where available (npm@5+)
COPY package*.json ./

# If you are building your code for production
RUN npm ci --omit=dev

# Bundle app source
COPY . .

EXPOSE 8080
CMD [ "node", "src/index.js" ]