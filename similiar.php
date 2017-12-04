	<style type="text/css">
		ul.akkordeon{
			width: 300px;
			background-color: #F0F0F0;			
		}
		.modal-body{
			display: block;
			height: 440px;
			overflow-y: scroll;
		}
		ul.akkordeon li > p{
			cursor: pointer;
			padding: 10px 10px;
			margin: 0;
			color: white;
			text-shadow: 0px 1px 1px rgb(73, 73, 73);
			text-align: center;
			font-size: 12px;
			font-family: sans-serif;
			background: #0C0C0C -webkit-gradient(linear, left top, left bottom, from(#868585), to(#666)) no-repeat;
		}
		ul.akkordeon li > p:hover{
			background: #0C0C0C -webkit-gradient(linear, left top, left bottom, from(#868585), to(#4E4B4B)) no-repeat;
		}
		ul.akkordeon li > p.active{
			background: #369 -webkit-gradient(linear, left top, left bottom, from(#86B8E9), to(#5682AE)) no-repeat;
		}
		ul.akkordeon li > div{
			display button: none;
			display: none;
			padding: 10px;
			font-size: 11px;
			line-height: 15px;

		}
		.flink{
			font-size: 12px;
			text-decoration: none;
			color: #2B9ED1;
		}
	</style>

<div class="dublicate">
	<h2 style="margin-bottom: 20px;">Найденный дубликаты: </h2>
	<ul class="akkordeon">
		<li>
			<?php $countDublicates = 0; ?>
			<?foreach($dublicates['name'] as $dub):?>
				<?foreach($dub as $d):?>
					<?$countDublicates= $countDublicates + 1;?>
				<?endforeach;?>
			<?endforeach;?>	
			<p class= "active">Дубликаты по имени(<?=$countDublicates?>)</p>		
			<div>
				<?foreach($dublicates['name'] as $dub):?>
					<?foreach($dub as $d):?>
						<a class="flink" target="_blank" href="<?=TERM.$d['name']?>"><?=$d['name']?> (открыть в новой вкладке)</a><br/>
					<?endforeach;?>
				<?endforeach;?>
			</div>			
		</li>
		<li>
			<p class= "active">Дубликаты по телефону(<?=count($dublicates['phone'])?>)</p>
			<div>
				<?$first = 0;?>	
				<?foreach($dublicates['phone'] as $dub):?>
					<?if(($lastname != $dub) && ($first = 1)){echo '<br/>';}?>
					<a class="flink" target="_blank" href="<?=TERM.$dub?>"><?=$dub?> (открыть в новой вкладке)</a><br/>
					<?$lastname = $dub;?>
					<?$first = 1;?>					
				<?endforeach;?>
			</div>
		</li>
		<li>
			<p class= "active">Дубликаты по Email(<?=count($dublicates['email'])?>)</p>
			<div>
				<?$first = 0;?>
				<?foreach($dublicates['email'] as $dub):?>
					<?if(($lastname != $dub) && ($first = 1)){echo '<br/>';}?>
					<a class="flink" target="_blank" href="<?=TERM.$dub?>"><?=$dub?> (открыть в новой вкладке)</a><br/>
					<?$lastname = $dub;?>
					<?$first = 1;?>					
				<?endforeach;?>
			</div>
		</li>				
	</ul>	
</div>
	<script type="text/javascript">
		$(document).ready(function(){	//страница загрузилась	
			$('ul.akkordeon li > p').click(function(){	//при клике на пункт меню:
				$(this).toggleClass('active');		//делаем данный пункт активным/неактивным
				$(this).next('div').slideToggle(200);	//раскрываем/скрываем следующий за "кликнутым" p блок div с эффектом slide
			});
			$('.flink').on('click', function(){
				$(this).css('color','#C0C0C4');
			});
		});
	</script>