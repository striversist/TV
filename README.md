TV
==

可以搜索电视节目


已知的bug
1. 生僻字转换utf8乱码：http://localhost/projects/TV/test/htmls/wuhan2_W6.htm 中的“雳剑”
	-> 使用third_party/pnews265可以改善这一状况，一些mb_convert_encoding无法转换的gb2312可以正常转换，但个别字还是存在错误，如“盃”字
2. 生僻符号转换失败：http://epg.tvsou.com/programgq/TV_279/Channel_1509/W1.htm "澳视高清"中的"(与<澳视体育>同步)"
	-> 是ProgramFilter中过滤的问题，已解决
3. start_collect_multi_cur.php不稳定，总是出现"Error $dom is not a instance of simple_html_dom"，估计是HTML内容抓取不全，导致不能正确转换为dom