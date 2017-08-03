import koaRouter from 'koa-router'
import api from '../controller/api'

const router = koaRouter()
router.prefix('/api')

router.get('/search', async(ctx, next) => {
	const {
		user,
		number,
		callback
	} = ctx.query

	ctx.set("Access-Control-Allow-Origin", "*") //设置cors，允许跨域

	const data = await api.search(user, number)
	if (callback) {  //允许JSONP请求
		ctx.body = `${callback}(${JSON.stringify(data)})`
	} else {
		ctx.body = data
	}

})

module.exports = router