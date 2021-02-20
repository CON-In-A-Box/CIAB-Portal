FROM node:14.15.5-alpine

WORKDIR "/home/node/app"
EXPOSE 8081
COPY "package.json" "package-lock.json" ./
RUN npm install
CMD npm start
