<?php	require_once(dirname(__FILE__).'/inc/config.inc.php');IsModelPriv('maintype');

/*
**************************
(C)2010-2015 phpMyWind.com
update: 2014-5-30 17:13:31
person: Feng
**************************
*/


//初始化参数
$tbname = '#@__maintype';
$gourl  = 'maintype.php';
$action = isset($action) ? $action : '';


//引入操作类
require_once(ADMIN_INC.'/action.class.php');


//添加二级分类
if($action == 'add')
{
	$parentstr = $doaction->GetParentStr();

	//自定义字段处理
	$fieldname  = '';
	$fieldvalue = '';
	$fieldstr   = '';

	$ids = GetDiyFieldCatePriv('5',$classid);
	if(!empty($ids)) {
		$dosql->Execute("SELECT * FROM `#@__diyfield` WHERE infotype=5 AND `id` IN ($ids) AND checkinfo=true ORDER BY orderid ASC");
		while($row = $dosql->GetArray())
	{
			$k = $row['fieldname'];
			$v = '';
			if(isset($_POST[$row['fieldname']]))
			{
				if(is_array($_POST[$row['fieldname']]))
				{
					foreach($_POST[$row['fieldname']] as $post_value)
					{
						if(@!get_magic_quotes_gpc())
    					{
							$v[] = addslashes($post_value);
						}
						else
						{
							$v[] = $post_value;
						}
					}
				}
				else
				{
					if(@!get_magic_quotes_gpc())
					{
						$v = addslashes($_POST[$row['fieldname']]);
					}
					else
					{
						$v = $_POST[$row['fieldname']];	
					}
				}
			}
			else
			{
				$v = '';
			}

			if(!empty($row['fieldcheck']))
			{
				if(!preg_match($row['fieldcheck'], $v))
				{
					ShowMsg($row['fieldcback']);
					exit();
				}
			}
	
			if($row['fieldtype'] == 'datetime')
			{
				$v = GetMkTime($v);
			}
			
			if($row['fieldtype'] == 'fileall')
			{
				$vTxt = isset($_POST[$row['fieldname'].'_txt']) ? $_POST[$row['fieldname'].'_txt'] : '';
	
				if(is_array($v) &&
				   is_array($vTxt))
				{
					$vNum = count($v);
					$vTmp = '';
			
					for($i=0;$i<$vNum;$i++)
					{
						if(@!get_magic_quotes_gpc())
						{
							$vTmp[] = $v[$i].','.addslashes($vTxt[$i]);
						}
						else
						{
							$vTmp[] = $v[$i].','.$vTxt[$i];
						}
					}
					
					$v = serialize($vTmp);
				}
			}
			
			if($row['fieldtype'] == 'checkbox')
			{
				@$v = implode(',',$v);
			}
	
			$fieldname  .= ", $k";
			$fieldvalue .= ", '$v'";
			$fieldstr   .= ", $k='$v'";
		}
	}

	$sql = "INSERT INTO `$tbname` (siteid, parentid, parentstr, classname, picurl, linkurl, orderid, checkinfo {$fieldname}) VALUES ('$cfg_siteid', '$parentid', '$parentstr', '$classname', '$picurl', '$linkurl', '$orderid', '$checkinfo' {$fieldvalue})";
	if($dosql->ExecNoneQuery($sql)) {
		header("location:$gourl");
		exit();
	}
}


//修改二级分类
else if($action == 'update')
{
	$parentstr = $doaction->GetParentStr();


	//更新所有关联parentstr
	if($parentid != $repid)
	{
		$childtbname = array('#@__infolist','#@__infoimg','#@__soft','#@__goods');

		//更新本类parentstr
		foreach($childtbname as $k=>$v)
		{
			$dosql->ExecNoneQuery("UPDATE `$v` SET parentid='".$parentid."', parentstr='".$parentstr."' WHERE classid=".$id);
		}

		//更新下级parentstr
		$doaction->UpParentStr($id, $childtbname, 'parentstr', 'classid');
	}

	//自定义字段处理
	$fieldname  = '';
	$fieldvalue = '';
	$fieldstr   = '';
	$classid = 0;

	$ids = GetDiyFieldCatePriv('5',$classid);
	if(!empty($ids))
	{
		$dosql->Execute("SELECT * FROM `#@__diyfield` WHERE infotype=5 AND `id` IN ($ids) AND checkinfo=true ORDER BY orderid ASC");
		while($row = $dosql->GetArray())
		{
			$k = $row['fieldname'];
			$v = '';
			if(isset($_POST[$row['fieldname']]))
			{
				if(is_array($_POST[$row['fieldname']]))
				{
					foreach($_POST[$row['fieldname']] as $post_value)
					{
						if(@!get_magic_quotes_gpc())
    					{
							$v[] = addslashes($post_value);
						}
						else
						{
							$v[] = $post_value;
						}
					}
				}
				else
				{
					if(@!get_magic_quotes_gpc())
					{
						$v = addslashes($_POST[$row['fieldname']]);
					}
					else
					{
						$v = $_POST[$row['fieldname']];	
					}
				}
			}
			else
			{
				$v = '';
			}

			if(!empty($row['fieldcheck']))
			{
				if(!preg_match($row['fieldcheck'], $v))
				{
					ShowMsg($row['fieldcback']);
					exit();
				}
			}
	
			if($row['fieldtype'] == 'datetime')
			{
				$v = GetMkTime($v);
			}
			
			if($row['fieldtype'] == 'fileall')
			{
				$vTxt = isset($_POST[$row['fieldname'].'_txt']) ? $_POST[$row['fieldname'].'_txt'] : '';
	
				if(is_array($v) &&
				   is_array($vTxt))
				{
					$vNum = count($v);
					$vTmp = '';
			
					for($i=0;$i<$vNum;$i++)
					{
						if(@!get_magic_quotes_gpc())
						{
							$vTmp[] = $v[$i].','.addslashes($vTxt[$i]);
						}
						else
						{
							$vTmp[] = $v[$i].','.$vTxt[$i];
						}
					}
					
					$v = serialize($vTmp);
				}
			}
			
			if($row['fieldtype'] == 'checkbox')
			{
				@$v = implode(',',$v);
			}
	
			$fieldname  .= ", $k";
			$fieldvalue .= ", '$v'";
			$fieldstr   .= ", $k='$v'";
		}
	}


	$sql = "UPDATE `$tbname` SET siteid='$cfg_siteid', parentid='$parentid', parentstr='$parentstr', classname='$classname', picurl='$picurl', linkurl='$linkurl', orderid='$orderid', checkinfo='$checkinfo'{$fieldstr} WHERE id=$id";
	if($dosql->ExecNoneQuery($sql))
	{
		header("location:$gourl");
		exit();
	}
}


//无条件返回
else
{
    header("location:$gourl");
	exit();
}
?>
