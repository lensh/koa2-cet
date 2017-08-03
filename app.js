import Koa from 'koa'
import views from 'koa-views'
import json from 'koa-json'
import bodyparser from 'koa-bodyparser'
import logger from 'koa-logger'
import index from './routes/index'
import api from './routes/api'

const app = new Koa()

// middlewares
app.use(bodyparser({
	enableTypes: ['json', 'form', 'text']
}))

app.use(json())
app.use(logger())
app.use(require('koa-static')(__dirname + '/public'))

app.use(views(__dirname + '/views', {
	extension: 'ejs'
}))

// logger
app.use(async(ctx, next) => {
	const start = new Date()
	await next()
	const ms = new Date() - start
	console.log(`${ctx.method} ${ctx.url} - ${ms}ms`)
})

// routes
app.use(index.routes(), index.allowedMethods())
app.use(api.routes(), api.allowedMethods())

module.exports = app