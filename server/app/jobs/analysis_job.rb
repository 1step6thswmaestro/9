class AnalysisJob < ActiveJob::Base

  def perform
    purchase_table = {}
    user_ids = Purchase.select(:user_id).uniq
    user_ids.each do |u|
      purchase_table[u.user_id] = []
      purchases = Purchase.where(user_id: u.user_id)
      purchases.each do |p|
        purchase_table[u].push(p.product_id)
      end
    end
    recommand_table = {}
    
    p purchase_table
    #AnalysisJob.delay_for(1.day).perform_later
  end
end
