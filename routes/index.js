import koaRouter from 'koa-router'

const router = koaRouter()

router.get('/', async (ctx, next) => {
  await ctx.render('index')   //render渲染一个页面
})

export default router
