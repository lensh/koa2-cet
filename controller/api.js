import superagent from 'superagent'
import cheerio from 'cheerio'

export default class Api {

	/**
	 * [search 查询成绩]
	 * @param  {[type]} user   [姓名]
	 * @param  {[type]} number [准考证号]
	 * @return {[type]}        [json数据]
	 */
	static async search(user, number) {

		// 抓取网页内容
		const data = await this.getData(user, number).catch((err) => {
			console.log(err)
		})

		// 解析网页内容
		const result = this.parseData(data)
		console.log(result)

		// 返回
		return result.total > 0 ? {
			"code": 200,
			"message": "查询成功",
			"data": result
		} : {
			"code": 400,
			"message": "查询失败，请检查你的信息是否无误"
		}
	}

	/**
	 * [getData 抓取网页内容]
	 * @param  {[type]} user   [姓名]
	 * @param  {[type]} number [准考证号]
	 * @return {[type]}        [json数据]
	 */
	static async getData(user, number) {
		return new Promise((resolve, reject) => {
			superagent
				.get('http://www.chsi.com.cn/cet/query')
				.set({
					'Referer': 'http://www.chsi.com.cn/cet/',
					'User-Agent': 'Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/45.0.2454.101 Safari/537.36'
				})
				.query({
					zkzh: number,
					xm: user
				})
				.end(function(err, sres) {
					if (err) {
						reject(err)
					}
					resolve(sres.text)
				})
		})
	}

	/**
	 * [parseData 解析网页内容]
	 * @param  {[type]} data [网页内容]
	 * @return {[type]}      [description]
	 */
	static parseData(data) {
		let name, school, type, number, total, listen, read, writing

		//解析数据
		const $ = cheerio.load(data)
		$('table.cetTable tr').each((index, ele) => {
			let text = $(ele).find('td').text().trim()
			let lastText = $(ele).children().last().text().trim()

			name = index == 0 ? text : name // 姓名
			school = index == 1 ? text : school // 学校
			type = index == 2 ? text : type //考试类别
			number = index == 4 ? text : number //准考证号
			total = index == 5 ? ($(ele).find('span.colorRed').text().trim() - 0) : total
			listen = index == 6 ? lastText : listen //听力
			read = index == 7 ? lastText : read //阅读
			writing = index == 8 ? lastText : writing //写作和翻译
		})

		return {
			name,
			school,
			type,
			number,
			total,
			listen,
			read,
			writing
		}
	}
}