<?xml version="1.0"?>
<xsl:stylesheet version="2.0"
								xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
								xmlns:php="http://php.net/xsl"
								xsl:extension-element-prefixes="php">

	<xsl:output method="xml" version="1.0" encoding="UTF-8" indent="yes"/>
	<xsl:namespace-alias stylesheet-prefix="php" result-prefix="xsl"/>

	<!--
	MySQL Workbench XML (.mwb)
	==== Author				: Thomas Schäfer <thomas.schaefer@query4u.de>
	==== Version		 	: 0.3
	==== Description 	:
	This XSL will transform a MySQL Workbench model XML file into a
	database schema file that is more reable.  
  This allows you to design your database
	model by using MySQL Workbench (models are saved in mwb archive format).
	-->
	<xsl:template match="/">

		<schema>

			<xsl:attribute name="project">
				<xsl:value-of select="//data/@project"/>
			</xsl:attribute>

			<xsl:attribute name="name">
				<xsl:value-of select="//data/@name"/>
			</xsl:attribute>

			<xsl:apply-templates mode="table"/>

			<comment>
				<author>Thomas Schäfer</author>
				<mail>thomas.schaefer@query4u.de</mail>
				<generatedAt>
					<xsl:value-of select="php:function('date', 'Y-m-d H:i')"/>
				</generatedAt>
				<createdAt>
					<xsl:value-of select="//data/@createdAt"/>
				</createdAt>
				<modifiedAt>
					<xsl:value-of select="//data/@modifiedAt"/>
				</modifiedAt>
			</comment>

		</schema>

	</xsl:template>

	<!-- ============================================================ TABLES template -->
	<xsl:template match="//data" mode="table">

		<!--
				configuration settings for controller install process
				edited in schema.Comments with MySQL Workbench Model Overview
		 -->
		<xsl:for-each select="//application">
			<application>
				<xsl:attribute name="name">
					<xsl:value-of select="@key"></xsl:value-of>
				</xsl:attribute>
				<xsl:attribute name="type">
					<xsl:value-of select="@type"></xsl:value-of>
				</xsl:attribute>
				<xsl:attribute name="context">
					<xsl:value-of select="@context"></xsl:value-of>
				</xsl:attribute>
				<xsl:attribute name="module">
					<xsl:value-of select="@module"></xsl:value-of>
				</xsl:attribute>
				<xsl:attribute name="connection">
					<xsl:value-of select="@connection"></xsl:value-of>
				</xsl:attribute>
				<xsl:attribute name="filter">
					<xsl:value-of select="@filter"></xsl:value-of>
				</xsl:attribute>
			</application>
		</xsl:for-each>

		<!-- tables and columns -->
		<xsl:for-each select="./value">
			<table>

				<xsl:attribute name="name">
					<xsl:value-of select="value[@key='name']"/>
				</xsl:attribute>

				<xsl:attribute name="type">
					<xsl:value-of select="value[@key='tableEngine']"/>
				</xsl:attribute>

				<xsl:attribute name="characterSet">
					<xsl:value-of select="value[@key='defaultCharacterSetName']"/>
				</xsl:attribute>

				<xsl:attribute name="auto">
					<xsl:value-of select="value[@key='nextAutoInc']"/>
				</xsl:attribute>

				<xsl:variable name="schema" select="../@schema"/>
				<xsl:attribute name="schema">
					<xsl:value-of select="$schema"/>
				</xsl:attribute>

				<!--
					audit,archive
					edited in table.Comments with MySQL Workbench Diagram
				-->
				<xsl:if test="string-length(value[@key='comment'])>0">
					<xsl:call-template name="explode">
						<xsl:with-param name="str" select="value[@key='comment']/text()"/>
					</xsl:call-template>
				</xsl:if>

				<!--xsl:variable name="TID" select="@id"/>
				<xsl:attribute name="id">
					<xsl:value-of select="$TID"/>
				</xsl:attribute-->

				<xsl:for-each select="./value">

					<xsl:choose>
						<xsl:when test="contains(@key,'columns')">
							<xsl:for-each select="./value">
								<xsl:variable name="ID" select="@id"/>
								<xsl:if test="string-length(value[@key='name'])>0">
									<column>

										<xsl:attribute name="name">
											<xsl:value-of select="./value[@key='name']"/>
										</xsl:attribute>

										<xsl:variable name="tp" select="substring(link[@key='simpleType'], string-length('com.mysql.rdbms.mysql.datatype.') + 1 )"/>

										<xsl:attribute name="type">
											<xsl:value-of select="$tp"/>
										</xsl:attribute>

										<xsl:variable name="o" select="//value[@struct-name='db.mysql.Index']/value[@key='name' and text()='PRIMARY']/../value[@content-struct-name='db.mysql.IndexColumn']/value/link[@key='referencedColumn']"/>
										<xsl:if test="$o=$ID">
											<xsl:attribute name="isPrimary">
												<xsl:value-of select="'true'"/>
											</xsl:attribute>
										</xsl:if>

										<xsl:variable name="a" select="value[@key='autoIncrement']"/>
										<xsl:if test="$a=1">
											<xsl:attribute name="auto">
												<xsl:value-of select="'true'"/>
											</xsl:attribute>
										</xsl:if>

										<xsl:variable name="r" select="value[@key='isNotNull']"/>
										<xsl:if test="$r=1">
											<xsl:attribute name="isRequired">
												<xsl:value-of select="'true'"/>
											</xsl:attribute>
										</xsl:if>

										<xsl:variable name="u" select="../../value[@key='indices']/*/value[@key='columns']/value/link"/>
										<xsl:variable name="u2" select="//value[@struct-name='db.mysql.Index']/value[@key='unique']"/>
										<xsl:if test="$u=$ID and $u2=1">
											<xsl:attribute name="isUnique">
												<xsl:value-of select="'true'"/>
											</xsl:attribute>
										</xsl:if>

										<xsl:choose>
											<xsl:when
											test="$tp='text' or $tp='longtext' or $tp='smalltext' or $tp='mediumtext' or $tp='blob' or $tp='longblog' or $tp='mediumblob'">
											</xsl:when>
											<xsl:when test="$tp='varchar'">
												<xsl:attribute name="size">
													<xsl:value-of select="value[@key='length']"/>
												</xsl:attribute>
											</xsl:when>
											<xsl:when test="$tp='char'">
												<xsl:attribute name="size">
													<xsl:value-of select="value[@key='length']"/>
												</xsl:attribute>
											</xsl:when>
											<xsl:when test="$tp='smalltext'">
												<xsl:attribute name="size">
													<xsl:value-of select="value[@key='length']"/>
												</xsl:attribute>
											</xsl:when>
											<xsl:otherwise>
												<xsl:attribute name="size">
													<xsl:value-of select="value[@key='precision']"/>
												</xsl:attribute>
											</xsl:otherwise>
										</xsl:choose>

										<xsl:variable name="d" select="value[@key='defaultValue']"/>
										<xsl:if test="$d!=''">
											<xsl:attribute name="default">
												<xsl:value-of select="$d"/>
											</xsl:attribute>
										</xsl:if>

										<xsl:variable name="idx" select="//value[@struct-name='db.mysql.Index']/value[@key='indexType' and text()='INDEX']/../value[@content-struct-name='db.mysql.IndexColumn']/value/link[@key='referencedColumn']"/>
										<xsl:if test="$ID=$idx">
											<xsl:attribute name="index">
												<xsl:value-of select="//value[@id=$ID]/value[@key='name']"/>
											</xsl:attribute>
										</xsl:if>

									</column>
								</xsl:if>
							</xsl:for-each>
						</xsl:when>
						<xsl:when test="contains(@key,'foreignKeys')">
							<xsl:for-each select="./value">
								<xsl:if test="string-length(value[@key='name'])>0">
									<association>
										<xsl:attribute name="name">
											<xsl:value-of select="./value[@key='name']"/>
										</xsl:attribute>
										<xsl:attribute name="reltype">
											<xsl:choose>
												<xsl:when test="value[@key='many']=1">one-to-many</xsl:when>
												<xsl:when test="value[@key='many']=0"><one></one>one-to-one
												</xsl:when>
											</xsl:choose>
										</xsl:attribute>
										<xsl:attribute name="column">
											<xsl:variable name="rc" select="./value[@key='columns']/link"/>
											<xsl:value-of select="//value[@id=$rc]/value[@key='name']"/>
										</xsl:attribute>

										<xsl:variable name="c" select="./value[@key='referencedColumns']/link"/>
										<xsl:variable name="t" select="./value[@key='referencedColumns']/link/../../link[@key='referencedTable']"/>

										<xsl:attribute name="refSchema">
											<xsl:value-of select="$schema"/>
										</xsl:attribute>

										<xsl:attribute name="refTable">
											<xsl:value-of select="//value[@id=$t]/value[@key='name']"/>
										</xsl:attribute>

										<xsl:attribute name="refColumn">
											<xsl:value-of select="//value[@id=$c]/value[@key='name']"/>
										</xsl:attribute>

										<xsl:attribute name="deleteRule">
											<xsl:value-of select="./value[@key='deleteRule']"/>
										</xsl:attribute>

										<xsl:attribute name="updateRule">
											<xsl:value-of select="./value[@key='updateRule']"/>
										</xsl:attribute>

									</association>
								</xsl:if>
							</xsl:for-each>
						</xsl:when>
					</xsl:choose>
				</xsl:for-each>

			</table>
		</xsl:for-each>

	</xsl:template>

	<!-- split function -->
	<xsl:template name="explode">
		<xsl:param name="str" select="''"/>
		<xsl:variable name="temp" select="concat($str, ',')"/>
		<xsl:variable name="head" select="substring-before($temp, ',')"/>
		<xsl:variable name="tail" select="substring-after($temp, ',')"/>
		<xsl:if test="$head != ''">
			<xsl:variable name="opt"></xsl:variable>
			<option>
				<xsl:value-of select="$head"/>
			</option>
			<xsl:call-template name="explode">
				<xsl:with-param name="str" select="normalize-space($tail)"/>
			</xsl:call-template>
		</xsl:if>
	</xsl:template>


</xsl:stylesheet>