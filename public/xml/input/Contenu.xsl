<?xml version="1.0" encoding="utf-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform" xmlns:fo="http://www.w3.org/1999/XSL/Format">
	<xsl:template name="CONTENU">
		<fo:page-sequence master-reference="page">
			<fo:static-content flow-name="xsl-region-before">
				<fo:block text-align="right" space-after="4pt">
					page<fo:page-number/>/<fo:page-number-citation ref-id="last-page"/>
				</fo:block>
				<fo:block-container height="78pt" background-image="img/palette.png" background-repeat="no-repeat">
					<fo:block margin-left="50pt" margin-top="55pt">
						<fo:inline color="blue" font-weight="bold" font-style="italic" font-size="18pt">Sur la toile</fo:inline>
					</fo:block>
					<fo:block-container position="absolute" height="100%" right="0cm" border="1px solid black" width="8cm" font-size="10pt">
								<xsl:apply-templates select="Commande" mode="client"/> 
					</fo:block-container>
				</fo:block-container>
			</fo:static-content>
			<fo:flow flow-name="xsl-region-body" font-size="10pt">
				<fo:block>
					<xsl:apply-templates select="Commande" mode="commande"/>
				</fo:block>
				<xsl:apply-templates/>
				<fo:block id="last-page"/>
			</fo:flow>
		</fo:page-sequence>
	</xsl:template>
	
	<xsl:template match="Commande" mode="client">
		<fo:block-container margin="4pt">
			<fo:block space-after="4pt">Client</fo:block>
			<fo:block>zzz</fo:block>
		</fo:block-container>
	</xsl:template>
	
	<xsl:template match="Commande" mode="commande">
		<fo:block-container border-bottom="1px solid black" space-after="12pt">
			<fo:block>Commande : <xsl:value-of select="@id"/></fo:block>
			<fo:block>Date : <xsl:value-of select="@date"/></fo:block>
		</fo:block-container>
	</xsl:template>
	
	
	<xsl:template match="Commande">
		<fo:table table-layout="fixed">
			<fo:table-column column-width="25mm"/>
			<fo:table-column column-width="25mm"/>
			<fo:table-column column-width="55mm"/>
			<fo:table-column column-width="25mm"/>
			<fo:table-column column-width="25mm"/>
				<fo:table-header text-align="center" background-color="yellow">
					<fo:table-row>
						<fo:table-cell padding="4pt" display-align="center" border="1px solid black">
							<fo:block font-weight="bold">Lig</fo:block>
						</fo:table-cell>
						<fo:table-cell padding="4pt" display-align="center" border="1px solid black">
							<fo:block font-weight="bold">Référence</fo:block>
						</fo:table-cell>
						<fo:table-cell padding="4pt" display-align="center" border="1px solid black">
							<fo:block font-weight="bold">Description</fo:block>
						</fo:table-cell>
						<fo:table-cell padding="4pt" display-align="center" border="1px solid black">
							<fo:block font-weight="bold">Qté</fo:block>
						</fo:table-cell>
						<fo:table-cell padding="4pt" display-align="center" border="1px solid black">
							<fo:block font-weight="bold">Prix</fo:block>
						</fo:table-cell>
					</fo:table-row>
				</fo:table-header>
				<fo:table-footer>
					<fo:table-row>
						<fo:table-cell padding="4pt" display-align="center" number-columns-spanned="4" border="1px solid black">
							<fo:block font-weight="bold" text-align="right" color="green">Total Commande</fo:block>
						</fo:table-cell>
						<fo:table-cell padding="4pt" display-align="center" border="1px solid black">
							<fo:block text-align="right">10000</fo:block>
						</fo:table-cell>
					</fo:table-row>
				</fo:table-footer>
				<fo:table-body>
					<xsl:for-each select="//Ligne">
						<fo:table-row>
							<fo:table-cell padding="4pt" display-align="center" border="1px solid black">
								<fo:block text-align="center">Volvo</fo:block>
							</fo:table-cell>
							<fo:table-cell padding="4pt" display-align="center" border="1px solid black">
								<fo:block>30 000</fo:block>
							</fo:table-cell>
							<fo:table-cell padding="4pt" display-align="center" border="1px solid black">
								<fo:block>Woaw, what a fucking car, Dude !!!!</fo:block>
							</fo:table-cell>
							<fo:table-cell padding="4pt" display-align="center" border="1px solid black">
								<fo:block text-align="center">1</fo:block>
							</fo:table-cell>
							<fo:table-cell padding="4pt" display-align="center" border="1px solid black">
								<fo:block text-align="right">1</fo:block>
							</fo:table-cell>
						</fo:table-row>
					</xsl:for-each>
				</fo:table-body>
				
		</fo:table>
	</xsl:template>
	
	
	
	<xsl:template match="Command">
		<fo:block text-align="center" font-weight="bold" space-before="30pt" font-size="30pt">
			<xsl:value-of select="@id"/>
		</fo:block>
		<xsl:apply-templates select="@date" />
		<fo:block font-size="4pt">
			<fo:leader leader-length="16cm" leader-pattern="rule"/>
		</fo:block>
		<xsl:apply-templates select="@date" />
	</xsl:template>
	<xsl:template match="@date">
		<fo:block text-align="left" space-before="10pt" font-size="14pt">
			<fo:basic-link external-destination="http://www.w3.org/TR" color="blue" text-decoration="underline">
				<xsl:value-of select="."/>
			</fo:basic-link>
		</fo:block>
	</xsl:template>
	<xsl:template match="@date">
		<fo:block text-align="left" space-before="10pt" font-size="14pt">
			<xsl:value-of select="."/>
		</fo:block>
	</xsl:template>
</xsl:stylesheet>