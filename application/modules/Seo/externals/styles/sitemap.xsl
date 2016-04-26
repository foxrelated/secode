<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="2.0" xmlns:html="http://www.w3.org/TR/REC-html40"
	xmlns:sitemap="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
	<xsl:output method="html" version="1.0" encoding="UTF-8"
		indent="yes" />
	<xsl:template match="/">
		<html xmlns="http://www.w3.org/1999/xhtml">
			<head>
				<title>XML Sitemap</title>
				<meta http-equiv="Content-Type" content="text/html; charset=utf-8"
					name="" />
				<style type="text/css">
body
{
 font-family: Arial, Tahoma,Verdana;
 font-size: 12px;
}
h1
{
 font-size: 16px;
}
#copyright
{
 color: #999;
 font-size: 10px;
 margin: 10px;
 padding: 5px;
}
#copyright a
{
 color: #666;
}
td
{
 font-size: 11px;
}
tr.header th
{
 background: #436088;
 color: #fff;
 font-size: 11px;
 padding-right: 25px;
 text-align: left;
}
tr.high
{
 background: #f2f2f2;
}
a
{
 color: #336699;
}
				</style>
			</head>
			<body>
				<h1>XML Sitemap</h1>

				<div id="content">
					<table cellpadding="5">
						<tr class="header">
							<th>URL</th>
							<th>Change Frequency</th>
							<th>Priority</th>
							<th>Last Modified</th>
						</tr>
						<xsl:for-each select="sitemap:urlset/sitemap:url">
							<tr>
								<xsl:if test="position() mod 2 != 1">
									<xsl:attribute name="class">high</xsl:attribute>
								</xsl:if>
								<td>
									<xsl:variable name="locURL">
										<xsl:value-of select="sitemap:loc" />
									</xsl:variable>
									<a href="{$locURL}" target="_blank">
										<xsl:value-of select="sitemap:loc" />
									</a>
								</td>
								<td>
									<xsl:value-of select="sitemap:changefreq" />
								</td>
								<td>
									<xsl:value-of select="sitemap:priority" />
								</td>
								<td>
									<xsl:value-of select="concat(substring(sitemap:lastmod,0,11),concat(' ', substring(sitemap:lastmod,12,5)))" />
								</td>
							</tr>
						</xsl:for-each>
					</table>
				</div>
       
			</body>
		</html>
	</xsl:template>
</xsl:stylesheet>